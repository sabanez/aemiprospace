jQuery(window).load( function(){
    // Variáveis globais
    var canvas, context, highlightCanvas, highlightContext, startX, startY, isDrawing = false;
    var highlights = [];
    var pdfsJsonSaveToSend = {};
    var pdfsJsonSaveToSendLasIndex = 0;
    var formulandoEnviando = false;
    var touchModeEnabled = false;

    // Função para carregar e renderizar o PDF
    function renderPDF(file, customFnRunAfterLoadedFile = false ) {
        document.body.classList.add('blocked-scroll-of-viewer-pdf');
        document.querySelector('#envixoifv__temp-modal-pdf').classList.add('enabled');

        // Criação de um novo FileReader
        var reader = new FileReader();

        // Evento de conclusão do carregamento do arquivo
        reader.onloadend = function() {
            // Converte os dados do arquivo para uma array de bytes
            var buffer = new Uint8Array(this.result);

            // Carrega o documento PDF usando a biblioteca pdf.js
            pdfjsLib.getDocument(buffer).promise.then(function(pdf) {
                // Renderiza todas as páginas do PDF em um único canvas
                var pageCount = pdf.numPages;

                // Renderiza cada página em um canvas temporário
                var renderPromises = [];
                for (var pageNumber = 1; pageNumber <= pageCount; pageNumber++) {
                    renderPromises.push(renderPageToCanvas(pdf, pageNumber));
                }

                // Aguarda todas as páginas serem renderizadas
                Promise.all(renderPromises).then(function(canvases) {
                    // Combina todas as páginas em um único canvas
                    var combinedCanvas = combineCanvases(canvases);
                    canvas = combinedCanvas;

                    // Exibe o canvas combinado no elemento pdfViewer
                    var pdfViewer = document.getElementById('envixoifv__pdf_viewer');
                    pdfViewer.appendChild(combinedCanvas);

                    // Cria o canvas para o destaque
                    highlightCanvas = document.createElement('canvas');
                    highlightContext = highlightCanvas.getContext('2d');
                    highlightCanvas.width = combinedCanvas.width;
                    highlightCanvas.height = combinedCanvas.height;

                    jQuery('#envixoifv__pdf_viewer').css({width: combinedCanvas.width + 'px' })

                    pdfViewer.appendChild(highlightCanvas);

                    // Adicionar os eventos de arrasto ao canvas de destaque
                    highlightCanvas.addEventListener('mousedown', startDrawing);
                    highlightCanvas.addEventListener('mousemove', drawHighlight);
                    highlightCanvas.addEventListener('mouseup', stopDrawing);
                    highlightCanvas.addEventListener('mouseleave', stopDrawing);

                    highlightCanvas.addEventListener('touchstart', touchStartDrawing);
                    highlightCanvas.addEventListener('touchmove', touchDrawHighlight);
                    highlightCanvas.addEventListener('touchend', touchStopDrawing);

                    if( customFnRunAfterLoadedFile ){
                        customFnRunAfterLoadedFile();
                        scrollToCenterHorizontal();
                    }
                });
            }, function (errorLog){
                alert( 'Oops, invalid file format!' );
                closeModalPDFEdit();
            } );
        };

        // Lê o arquivo como uma array de bytes
        reader.readAsArrayBuffer(file);
    }

    //valida se o touch mode está habilitado
    function touchStartDrawing(event){
        if( touchModeEnabled ){
            return startDrawing(event);
        }
        return;
    }

    //valida se o touch mode está habilitado
    function touchDrawHighlight( event ){
        if( touchModeEnabled ){
            return drawHighlight( event );
        }
        return;
    }

    //valida se o touch mode está habilitado
    function touchStopDrawing( event ){
        if( touchModeEnabled ){
            return stopDrawing( event );
        }
        return;
    }

    // Função para renderizar uma página em um canvas
    function renderPageToCanvas(pdf, pageNumber) {
        return pdf.getPage(pageNumber).then(function(page) {
            var viewport = page.getViewport({ scale: 1.5 });

            // Cria um canvas para a página
            var pageCanvas = document.createElement('canvas');
            var pageContext = pageCanvas.getContext('2d');
            pageCanvas.width = viewport.width;
            pageCanvas.height = viewport.height;

            // Renderiza a página no canvas
            return page.render({
                canvasContext: pageContext,
                viewport: viewport
            }).promise.then(function() {
                return pageCanvas;
            });
        });
    }

    // Função para combinar vários canvases em um único canvas
    function combineCanvases(canvases) {
        // Calcula o tamanho total do canvas combinado
        var combinedWidth = 0;
        var combinedHeight = 0;
        canvases.forEach(function(canvas) {
            combinedWidth = Math.max(combinedWidth, canvas.width);
            combinedHeight += canvas.height;
        });

        // Cria um canvas combinado com o tamanho calculado
        var combinedCanvas = document.createElement('canvas');
        combinedCanvas.width = combinedWidth;
        combinedCanvas.height = combinedHeight;
        var combinedContext = combinedCanvas.getContext('2d');

        // Combina os canvases individuais no canvas combinado
        var offsetY = 0;
        canvases.forEach(function(canvas) {
            combinedContext.drawImage(canvas, 0, offsetY);
            offsetY += canvas.height;
        });

        return combinedCanvas;
    }

    // Função para iniciar o destaque
    function startDrawing(event) {
        event.preventDefault(); // Impede o comportamento padrão do toque

        var touch = event.touches ? event.touches[0] : event;
        startX = touch.clientX - highlightCanvas.getBoundingClientRect().left;
        startY = touch.clientY - highlightCanvas.getBoundingClientRect().top;
        isDrawing = true;
    }

    // Função para realizar o destaque
    function drawHighlight(event) {
        event.preventDefault(); // Impede o comportamento padrão do toque

        if (!isDrawing) return;

        var touch = event.touches ? event.touches[0] : event;
        var currentX = touch.clientX - highlightCanvas.getBoundingClientRect().left;
        var currentY = touch.clientY - highlightCanvas.getBoundingClientRect().top;

        // Desenhar todos os destaques anteriores
        redrawHighlights();

        // Desenhar o destaque atual
        highlightContext.fillStyle = 'rgba(0, 0, 0, 1)';
        highlightContext.fillRect(startX, startY, currentX - startX, currentY - startY);
    }

    // Função para parar o destaque
    function stopDrawing(event) {
        event.preventDefault(); // Impede o comportamento padrão do toque

        if (isDrawing) {
            var touch = event.changedTouches ? event.changedTouches[0] : event;
            var currentX = touch.clientX - highlightCanvas.getBoundingClientRect().left;
            var currentY = touch.clientY - highlightCanvas.getBoundingClientRect().top;

            // Cria o elemento de destaque
            var highlightElement = document.createElement('div');
            highlightElement.classList.add('highlight');
            highlightElement.style.left = startX + 'px';
            highlightElement.style.top = startY + 'px';
            highlightElement.style.width = ( (currentX - startX) - 0 ) + 'px';
            highlightElement.style.height = ( (currentY - startY) - 0 ) + 'px';

            // Cria o botão de remoção
            var removeButton = document.createElement('button');
            removeButton.classList.add('remove-button');
            removeButton.innerText = 'x';

            // Adiciona o evento de remoção ao botão
            removeButton.addEventListener('click', function() {
                highlightElement.remove();
                removeHighlight(highlightElement);
                redrawHighlights();
            });

            // Adiciona o botão de remoção ao destaque
            highlightElement.appendChild(removeButton);

            // Adiciona o destaque ao pdfViewer
            var pdfViewer = document.getElementById('envixoifv__pdf_viewer');
            pdfViewer.appendChild(highlightElement);

            // Armazena as informações do destaque na matriz de destaques
            var highlight = {
                element: highlightElement,
                startX: startX,
                startY: startY,
                width: currentX - startX,
                height: currentY - startY
            };
            highlights.push(highlight);
        }

        isDrawing = false;
    }

    // Função para remover um destaque específico
    function removeHighlight(element) {
        for (var i = 0; i < highlights.length; i++) {
            if (highlights[i].element === element) {
                highlights.splice(i, 1);
                break;
            }
        }
    }

    // Função para redesenhar todos os destaques
    function redrawHighlights() {
        highlightContext.clearRect(0, 0, highlightCanvas.width, highlightCanvas.height);

        for (var i = 0; i < highlights.length; i++) {
            var highlight = highlights[i];
            highlightContext.fillStyle = 'rgba(0, 0, 0, 1)';
            highlightContext.fillRect(highlight.startX, highlight.startY, highlight.width, highlight.height);
        }
    }

    function closeModalPDFEdit() {
        document.body.classList.remove('blocked-scroll-of-viewer-pdf');
        document.querySelector('#envixoifv__temp-modal-pdf').classList.remove('enabled');
        setTimeout(function(){
            document.querySelector('#envixoifv__temp-modal-pdf').remove();  
        }, 500);
    }

    /**
     * Faz o scroll horizontal automático para centralizar o elemento com o ID especificado.
     * @param {string} elementId - O ID do elemento alvo para centralização.
     */
    function scrollToCenterHorizontal(elementId = 'envixoifv__pdf_viewer__inner') {
                
        // Obtém o elemento pelo ID
        var elemento = document.getElementById(elementId);

        if (elemento) {
            // Obtém a largura total do elemento
            var elementoLargura = elemento.scrollWidth;

            // Obtém a largura do elemento visível
            var elementoVisivelLargura = elemento.clientWidth;

            // Calcula o deslocamento necessário para centralizar o elemento
            var deslocamento = (elementoLargura - elementoVisivelLargura) / 2;

            // Realiza o scroll horizontal dentro do elemento para o deslocamento calculado
            elemento.scrollLeft = deslocamento;
        }
    }

    function sanitizeHTMLPrevent(value) {
        var tempElement = document.createElement('div');
        tempElement.textContent = value;
        return tempElement.innerHTML;
    }

    function removeDeletePDFByElement( element ){
        if( jQuery( element ).find('.envixoifv__ticket__attachment_input_file__box__pdf_input').length > 0 ){
            delete pdfsJsonSaveToSend[jQuery( element ).find('.envixoifv__ticket__attachment_input_file__box__pdf_input').data('id')];
        }
    }
    
    function removeElementsPdf( element ){
        if( jQuery( element ).find('.envixoifv__ticket__attachment_input_file__box__pdf_input').length > 0 ){
            removeDeletePDFByElement( element );
            jQuery( element ).find('.envixoifv__ticket__attachment_input_file__box__pdf_input').remove();
        }
    }

    function saveModalPDFEdit( element, filename ){
        //reset  
        removeElementsPdf( element );

        // Cria um novo canvas para gerar a imagem com os destaques
        var highlightedCanvas = document.createElement('canvas');
        var highlightedContext = highlightedCanvas.getContext('2d');
        highlightedCanvas.width = canvas.width;
        highlightedCanvas.height = canvas.height;

        // Desenha a imagem do PDF no novo canvas
        highlightedContext.drawImage(canvas, 0, 0);

        // Desenha os destaques no novo canvas
        for (var i = 0; i < highlights.length; i++) {
            var highlight = highlights[i];
            highlightedContext.fillStyle = 'rgba(0, 0, 0, 1)';
            highlightedContext.fillRect(highlight.startX, highlight.startY, highlight.width, highlight.height);
        }

        // Converte o canvas para um arquivo Blob
        highlightedCanvas.toBlob(function(blob) {
            pdfsJsonSaveToSendLasIndex = (pdfsJsonSaveToSendLasIndex+1);
            pdfsJsonSaveToSend[pdfsJsonSaveToSendLasIndex] = {
                name: filename,
                blob: blob
            };
            
            let inputElementBefore = jQuery( element ).find(".envixoifv__ticket__attachment_input_file__box__label input[type='file']" );
            jQuery( '<div class="envixoifv__ticket__attachment_input_file__box__pdf_input" data-id="' + pdfsJsonSaveToSendLasIndex + '">' + filename + '</div>' ).insertBefore( inputElementBefore );

            closeModalPDFEdit();
        });

        jQuery( "body" ).append( "<div class=\"envixoifv__ticket__toast-pdf\"><div><svg xmlns=\"http://www.w3.org/2000/svg\" height=\"1.5em\" viewBox=\"0 0 512 512\"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d=\"M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z\"/></svg> ¡El archivo <strong>"+ filename +"</strong> se cargó correctamente!</div></div>" );

        setTimeout(function(){
            jQuery( ".envixoifv__ticket__toast-pdf" ).addClass( 'enabled' );

            setTimeout(function(){
                jQuery( ".envixoifv__ticket__toast-pdf" ).addClass( 'remove-toast' );

                setTimeout(function(){
                    jQuery( ".envixoifv__ticket__toast-pdf" ).remove();
                }, 1000);
            }, 5000);

        }, 100);
    }

    function generateElementModalPdfEdit( file, element ){
        let filename = file.name;

        jQuery( [
            '<div id="envixoifv__temp-modal-pdf">',
                '<div id="envixoifv__container-render">',
                    '<div id="envixoifv__pdf_viewer__container">',
                        '<h2>Seleccione los datos que desea marcar como confidenciales a continuación:</h2>',
                        '<div id="envixoifv__pdf_viewer__inner"><div id="envixoifv__pdf_viewer"></div></div>',
                    '</div>',
                    '<div id="envixoifv__pdf_btns">',
                    '</div>',
                '</div>',
            '</div>'
        ].join('') ).appendTo('.page-general-container')

        // Limpa o conteúdo do visualizador
        document.getElementById('envixoifv__pdf_viewer').innerHTML = '';

        // Reinicia as variáveis de destaque
        startX = 0;
        startY = 0; 
        isDrawing = false;
        highlights = [];

        // Chama a função para renderizar o PDF
        renderPDF( file, function(){

            jQuery( 
                [
                    '<div>',
                        '<div id="envixoifv__temp-modal-tools">',
                            '<div id="envixoifv__temp-modal-tools__text">',
                                'Modo de edición:',
                            '</div>',
                            '<div id="envixoifv__temp-modal-tools__action">',
                                '<span>',
                                    '<button type="button" id="envixoifv__temp-modal-tools__scroll" class="active">',
                                        '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8h32v96H128V192c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6V288h96v96H192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8H288V288h96v32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6v32H288V128h32c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"/></svg>',
                                    '</button>',
                                    '<span>Mover</span>',
                                '</span>',
                                '<span>',
                                    '<button type="button" id="envixoifv__temp-modal-tools__edit">',
                                        '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M0 448c0 17.7 14.3 32 32 32s32-14.3 32-32l0-336c0-8.8 7.2-16 16-16l336 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32C35.8 32 0 67.8 0 112L0 448zm160 0a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm192 0a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm-96 0a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm192 0a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM416 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm0 32a32 32 0 1 0 0 64 32 32 0 1 0 0-64zm0-128a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/></svg>',
                                    '</button>',
                                    '<span>Marcar</span>',
                                '</span>',
                            '</div>',
                        '</div>',
                        '<div id="envixoifv__temp-modal-btns-actions">',
                            '<button class="btn btn-primary" id="envixoifv__temp-modal--btn-cancel">Cancelar</button><button class="btn btn-secondary" id="envixoifv__temp-modal--btn-save">Confirmar y guardar</button>',
                        '</div>',
                    '</div>'
                ].join('')
            ).appendTo('#envixoifv__temp-modal-pdf #envixoifv__pdf_btns');

            document.querySelector('#envixoifv__temp-modal--btn-cancel').addEventListener('click', function(){
                closeModalPDFEdit();
            });
    
            document.querySelector('#envixoifv__temp-modal--btn-save').addEventListener('click', function(){
                saveModalPDFEdit(element, filename);
            });

            document.querySelector('#envixoifv__temp-modal-tools__scroll').addEventListener('click', function(){
                this.classList.add('active');
                document.querySelector('#envixoifv__temp-modal-tools__edit').classList.remove('active');
                touchModeEnabled = false;
            });

            document.querySelector('#envixoifv__temp-modal-tools__edit').addEventListener('click', function(){
                this.classList.add('active');
                document.querySelector('#envixoifv__temp-modal-tools__scroll').classList.remove('active');
                touchModeEnabled = true;
            });
        } );
    }

    window.envixoifv__custom_element__ticket__attachment_input_file__box = function( envixo_integration_with_freshdesk_frontend_vars ){
        return [
            '<div class="envixoifv__ticket__attachment_input_file__box">',
                '<button type="button" class="envixoifv__ticket__attachment_input_file__box__remove">x</button>',
                '<label class="envixoifv__ticket__attachment_input_file__box__label">',
                    '<input type="file" name="envixoifv_file[]" />',
                    '<span>' + envixo_integration_with_freshdesk_frontend_vars.select_button + '</span>',
                '</label>',
            '</div>'
        ].join('');
    }

    window.envixoifv__custom_envixoifv__ticket__attachment_input_file__box__remove__function_before = function( element ){
        removeElementsPdf( jQuery(element).closest('.envixoifv__ticket__attachment_input_file__box') );
    }

    jQuery('body').on('change', '.envixoifv__ticket__attachment_input_file__box__label input', function(){
        if( this.files[0] ){
            if( ( this.files[0].size/1024/1024 ) > 20  ){
            } else {
                //vamos verificar se aqui é um pdf       
                if( this.files[0]['type'] == 'application/pdf' ){
                    generateElementModalPdfEdit( this.files[0], jQuery(this).closest('.envixoifv__ticket__attachment_input_file__box') );
                    jQuery(this).val('');
                } else {
                    removeElementsPdf( jQuery(this).closest('.envixoifv__ticket__attachment_input_file__box') );
                }
            }
        }
    });

    if( jQuery('.envixoifv__add-ticket__form, .envixoifv__view-ticket__reply-form').length > 0 ){
        jQuery('.envixoifv__add-ticket__form, .envixoifv__view-ticket__reply-form').on('submit', function( e ){
            e.preventDefault();

            if( formulandoEnviando == false ){
                formulandoEnviando = true;
                
                if( jQuery('.envixoifv__add-ticket__container-btn button[type="submit"]').length ){
                    jQuery('.envixoifv__add-ticket__container-btn button[type="submit"]').prop('disabled', true);
                    jQuery('.envixoifv__add-ticket__container-btn button[type="submit"]').addClass('opacity-5 opacity-50 loading');
                }

                if( jQuery('.envixoifv__view-ticket__reply__container-btn button[type="submit"]').length ){
                    jQuery('.envixoifv__view-ticket__reply__container-btn button[type="submit"]').prop('disabled', true);
                    jQuery('.envixoifv__view-ticket__reply__container-btn button[type="submit"]').addClass('opacity-5 opacity-50 loading');
                }

                var form = jQuery(this);
                var url = window.location.href;
                var formData = new FormData(form[0]);
            
                console.log( pdfsJsonSaveToSend );

                if( pdfsJsonSaveToSend && Object.keys(pdfsJsonSaveToSend).length ){
                    Object.keys(pdfsJsonSaveToSend).forEach(function( index ){
                        formData.append('envixoifv_file[]', pdfsJsonSaveToSend[index].blob, pdfsJsonSaveToSend[index].name+'.png' );
                    });
                }
    
                jQuery.ajax({
                    url: url,
                    type: 'POST',
                    data: formData, 
                    contentType: false, 
                    processData: false, 
                    success: function(response) {
                        window.location = response.url;
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    }
});