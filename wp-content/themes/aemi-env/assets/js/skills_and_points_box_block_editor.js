wp.blocks.registerBlockType('brad/border-box', {
    title: 'Bloque de Habilidades y puntos asociados al contenido.',
    icon: 'align-none',
    category: 'common',
    attributes: {
      content: {type: 'string'},
      color: {type: 'string'}
    },
    
  /* This configures how the content and color fields will work, and sets up the necessary elements */
    
    edit: function(props) {
        return React.createElement(
            "div",
            {
                dangerouslySetInnerHTML: { __html: template_skills_and_points_box.template },
            }
        );
    },
    save: function(props) {
      return wp.element.createElement(
        "div",
        { class: "skills_and_points_box__in__content"},
        '[skills_and_points_box]'
      );
    }
  })