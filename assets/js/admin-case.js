/* OLT — Case edit screen admin JS.
 *   - Live preview of the plate (logo + slash + tagline)
 *   - Media picker for logo and video thumbs
 *   - Drag-and-drop reordering of videos
 *   - Provider badges (Vimeo / YouTube) inferred from URL
 *   - Object-position preset chips
 */
(function ($) {
  if (!$) return;

  // ---------- Live preview of the plate ----------
  function refreshPlatePreview() {
    var $logo = $('#olt_plate_logo_id');
    var $w = $('#olt_plate_logo_width');
    var $tag = $('#olt_plate_tagline');
    var logoUrl = $('.olt-picker__preview img').first().attr('src') || '';
    var width = parseInt($w.val(), 10) || 0;
    var tag = ($tag.val() || '').replace(/<br\s*\/?>/gi, '\n');

    var $preview = $('.olt-preview-plate');
    if (!$preview.length) return;
    var $previewLogo = $preview.find('.olt-preview-plate__logo');
    if (logoUrl) {
      $previewLogo.attr('src', logoUrl).show();
      if (width > 0) $previewLogo.css('width', width + 'px');
    } else {
      $previewLogo.hide();
    }
    var html = tag.split('\n').map(function (line) {
      return $('<div/>').text(line).html();
    }).join('<br>');
    $preview.find('.olt-preview-plate__tag').html(html);
  }

  $(document).on('input change', '#olt_plate_tagline, #olt_plate_logo_width, #olt_plate_logo_id', refreshPlatePreview);

  // ---------- Media picker (logo) ----------
  var logoFrame;
  $(document).on('click', '#olt-pick-logo', function (e) {
    e.preventDefault();
    if (logoFrame) { logoFrame.open(); return; }
    logoFrame = wp.media({
      title: 'Selecionar logo',
      button: { text: 'Usar este logo' },
      multiple: false
    });
    logoFrame.on('select', function () {
      var att = logoFrame.state().get('selection').first().toJSON();
      $('#olt_plate_logo_id').val(att.id).trigger('change');
      $('#olt-pick-logo').closest('.olt-picker').find('.olt-picker__preview')
        .html('<img src="' + att.url + '" alt="">');
      refreshPlatePreview();
    });
    logoFrame.open();
  });

  $(document).on('click', '#olt-clear-logo', function (e) {
    e.preventDefault();
    $('#olt_plate_logo_id').val('').trigger('change');
    $('#olt-pick-logo').closest('.olt-picker').find('.olt-picker__preview').empty();
    refreshPlatePreview();
  });

  // ---------- Object-position preset chips ----------
  var presets = [
    { label: 'Centro', value: 'center' },
    { label: 'Topo', value: 'center top' },
    { label: 'Esquerda', value: 'left center' },
    { label: 'Direita', value: 'right center' },
    { label: 'Esq. alto', value: '20% 30%' },
    { label: 'Dir. alto', value: '80% 30%' }
  ];
  function buildPresets() {
    var $input = $('#olt_mobile_pos');
    if (!$input.length || $input.next('.olt-preset-chips').length) return;
    var $wrap = $('<div class="olt-preset-chips"></div>');
    presets.forEach(function (p) {
      var $btn = $('<button type="button" class="olt-preset-chip"></button>').text(p.label).attr('data-value', p.value);
      $wrap.append($btn);
    });
    $input.after($wrap);
    syncPresetActive();
    $wrap.on('click', '.olt-preset-chip', function () {
      $input.val($(this).attr('data-value')).trigger('input');
      syncPresetActive();
    });
    $input.on('input', syncPresetActive);
  }
  function syncPresetActive() {
    var v = ($('#olt_mobile_pos').val() || '').trim();
    $('.olt-preset-chip').each(function () {
      $(this).toggleClass('is-active', $(this).attr('data-value') === v);
    });
  }

  // ---------- Video thumb picker ----------
  $(document).on('click', '.olt-pick-thumb', function (e) {
    e.preventDefault();
    var $btn = $(this);
    var frame = wp.media({ title: 'Thumb do vídeo', multiple: false });
    frame.on('select', function () {
      var att = frame.state().get('selection').first().toJSON();
      $btn.closest('td').find('.olt-thumb-id').val(att.id);
      $btn.closest('td').find('.olt-picker__preview')
        .html('<img src="' + att.url + '" alt="">');
    });
    frame.open();
  });

  // ---------- Remove video row ----------
  $(document).on('click', '.olt-videos__remove', function (e) {
    e.preventDefault();
    $(this).closest('tr').remove();
    reindexVideoRows();
  });

  // ---------- Add video row ----------
  $(document).on('click', '#olt-add-video', function (e) {
    e.preventDefault();
    var template = window.OLT_VIDEO_ROW_TEMPLATE || '';
    var idx = $('#olt-videos tbody tr').length;
    var html = template.replace(/__IDX__/g, idx);
    $('#olt-videos tbody').append(html);
  });

  // ---------- Provider badge on URL change ----------
  function providerFromUrl(url) {
    if (!url) return '';
    if (/vimeo\.com/i.test(url)) return 'vimeo';
    if (/youtu\.?be/i.test(url)) return 'youtube';
    return '';
  }
  function refreshProviderBadge($input) {
    var $cell = $input.closest('td');
    var $badge = $cell.find('.olt-videos__provider');
    var provider = providerFromUrl($input.val());
    $badge.removeClass('olt-videos__provider--vimeo olt-videos__provider--youtube');
    if (provider) {
      $badge.addClass('olt-videos__provider--' + provider).text(provider).show();
    } else {
      $badge.hide();
    }
  }
  $(document).on('input change', '.olt-videos__url', function () {
    refreshProviderBadge($(this));
  });

  // ---------- Sortable videos ----------
  function reindexVideoRows() {
    $('#olt-videos tbody tr').each(function (i) {
      $(this).find('input, button').each(function () {
        if (this.name) {
          this.name = this.name.replace(/olt_videos\[\d+\]/, 'olt_videos[' + i + ']');
        }
      });
    });
  }
  function initSortable() {
    var $tbody = $('#olt-videos tbody');
    if (!$tbody.length) return;
    if (!$.fn.sortable) return;
    $tbody.sortable({
      handle: '.olt-videos__drag',
      axis: 'y',
      placeholder: 'olt-videos__placeholder',
      forcePlaceholderSize: true,
      tolerance: 'pointer',
      update: reindexVideoRows
    });
  }

  $(function () {
    refreshPlatePreview();
    buildPresets();
    initSortable();
    $('.olt-videos__url').each(function () { refreshProviderBadge($(this)); });
  });
})(window.jQuery);
