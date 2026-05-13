# Alexandre Oltramari — Tema WordPress

Tema institucional clássico (não block theme) que reproduz o site single-page com efeito de stacking scroll. Os cases são gerenciados via Custom Post Type; hero, intro e rodapé via Customizer.

## Instalação

1. Copie a pasta `alexandre-oltramari/` para `wp-content/themes/`.
2. No admin, vá em **Aparência → Temas** e ative "Alexandre Oltramari".
3. Configure o link permanente: **Configurações → Links permanentes → Nome do post**.
4. Crie uma página vazia, defina ela como **Página inicial** em **Configurações → Leitura → Sua página inicial exibe → Uma página estática**.

## Conteúdo

### Cases (CPT)
- Menu lateral: **Cases**.
- Cada case representa **um par** seção-foto + seção-texto.
- Campos:
  - **Título** = título do post (usado como subtítulo do bloco de texto se não houver subtítulo).
  - **Conteúdo do editor** = body do bloco de texto (parágrafo abaixo do carrossel).
  - **Imagem destacada** = foto grande de fundo (desktop). Tamanho ideal: 2400×1080 ou maior.
  - **Texto do plate** = headline no card preto (ex: `É daqui<br>pra melhor`). Aceita `<br>`.
  - **Subtítulo** = h2 acima do carrossel (ex: `O povo venceu de novo`).
  - **Logo da campanha** = PNG/SVG transparente.
  - **Largura do logo (px)** = ex: 170.
  - **object-position no mobile** (opcional) = crop alternativo para imagens cuja face fica fora do centro (ex: `15% center` para Simone Tebet).
  - **Vídeos** = lista repeater (thumb + URL YouTube + rótulo). O carrossel aparece se há ≥1 vídeo.
- Ordem na home: **menu_order** (use o campo "Atributos da página → Ordem", menor primeiro).

### Hero, Intro, Rodapé
- **Aparência → Personalizar → Homepage — Conteúdo**:
  - Imagem do hero
  - Texto da intro
  - Título, bio, WhatsApp do rodapé

## Importar conteúdo inicial

Após ativar o tema, vá em **Ferramentas → OLT seed** e clique em "Rodar seed". Cria os 9 cases do site original com todos os textos. **As imagens (capa, logo, thumbs de vídeo) precisam ser anexadas manualmente** pelo editor de cada case — envie-as via Mídia e selecione no respectivo campo.

## Estrutura

```
alexandre-oltramari/
├── style.css                 ← cabeçalho WP (estilos reais em assets/css/styles.css)
├── functions.php             ← setup + enqueue + includes
├── header.php / footer.php   ← chrome
├── front-page.php            ← homepage (loop dos cases)
├── inc/
│   ├── cpt-cases.php         ← CPT + meta boxes
│   ├── customizer.php        ← hero / intro / rodapé
│   ├── helpers.php           ← utilitários (queries, YouTube parser)
│   └── seeder.php            ← importador one-shot
├── template-parts/
│   ├── hero.php
│   ├── intro.php
│   ├── case-image.php
│   ├── case-text.php
│   └── site-footer.php
└── assets/
    ├── css/styles.css        ← copiado da raiz do projeto
    ├── js/                   ← stacking-scroll, carousel, lightbox, menu
    └── images/               ← todas as 38 webp + 9 svg
```

## Notas de implementação

- **Stacking scroll**: o JS (`stacking-scroll.js`) busca todas as `.snap` na ordem do DOM. Como o `front-page.php` renderiza hero → intro → loop(cases) → footer, a ordem fica automaticamente correta.
- **Menu popup**: construído pelo `menu.js` lendo `window.OltScroll.getSections()`. Pula `.case-text` para não duplicar. Funciona idêntico ao site estático.
- **Carrossel**: o `data-per-page` é derivado da contagem de vídeos (1 ou 2). Setas e paginação aparecem apenas quando há mais de uma página.
- **Lightbox de vídeo**: o `<div class="video-card">` recebe `data-video-url="https://youtube.com/..."`. O `lightbox.js` original espera esse atributo (verificar implementação atual; pode precisar ajuste se ela leu de outra forma).
- **Performance**: `wp_head` faz preload do hero-bg.webp. Scripts são deferidos.
- **i18n**: domínio `alexandre-oltramari`. Adicione `.po/.mo` em `languages/` quando quiser traduzir.

## Próximas evoluções sugeridas

- Migrar para um tipo de bloco Gutenberg "Case" se quiser editar visualmente.
- Versão mobile do background do case (mostrar `picture` com source mobile).
- Carregamento lazy nativo das imagens dos vídeos.
- Endpoint REST para tornar a homepage consumível por outro front (PWA, app).
