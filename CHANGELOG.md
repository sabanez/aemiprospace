# Changelog

## 2026-11-19
- aemi-env: pagina de registro (`template-page-register.php` + `assets/css/styles.css`).
  Panel azul lateral sin triangulo ni cruces, con el logo AEMI (blanco/naranja) centrado arriba
  y el texto "Tienes una cuenta" mas abajo. En movil: logo centrado, "Espacio profesional",
  texto "Ya tienes una cuenta? Iniciar sesion" (enlace #ff8200), formulario desplazado hacia
  abajo y boton de acceso debajo del formulario.
  Labels flotantes de Profile Builder en movil: 13px y hasta dos lineas para que no se salgan
  del campo.

## 2026-11-11
- Se anade `.gitignore` en la raiz del repo: ignora `wp-config.php`, `.env`, volcados
  `*.sql`/`*.sql.gz`, `*.log`, `*.zip`, `/backup/`, `/wp-content/ai1wm-backups/`,
  `.DS_Store` y `.bash_history`.
  Motivo: el repo se separo de un repo comun con otros proyectos y quedaban sin versionar
  ficheros sensibles que un `git add .` habria subido a GitHub.
  Limite: solo afecta a ficheros aun no versionados; si algo sensible ya se hubiera
  commiteado habria que sacarlo del indice (`git rm --cached`).
  Prueba: `git status` ya no muestra esos ficheros y `git check-ignore -v wp-config.php`
  indica la regla que lo ignora.
