$(document).ready(initSelectize);

function intercomTrack(e) {
  if (!$(e.target).attr('intercom-tracked'))
    var event = $(e.target).parent().attr('intercom-tracked');
  else
    var event = $(e.target).attr('intercom-tracked');

  if (event)
    Intercom('trackEvent', event);

  return false;
}

function initSelectize()
{
  $('#nextRow').click(function() {
    buildSampleRow();
  });

  $('#configuration_form_submit_btn').on('click', function (e) {
    var error = false;
    var validEmptyInputs = ['deleteRow', 'deleteRowWhereNot', 'remove', 'deleteRowWhereContains', 'deleteRowWhereNotContains', 'mergeColumns'];
    var validEmptySelects = ['mergeRows', 'splitCombinations'];

    var unitVal = $('#unity').removeClass('error').val();
    var r = new RegExp(/([a-zA-Z\s])+/gi);
    if (!r.exec(unitVal)) {
      $('#unity').addClass('error');
      displayAlert(false, 'Unit must only contain letters');
      return false;
    }

    $('tbody#operations td input[type="text"]').each(function (i, input) {
      //get the adjustment type
      var adj_type = $(input).parent().parent().find('input[type="hidden"]').val();
      if ($(input).val().length === 0 && validEmptyInputs.indexOf(adj_type) === -1) {
        error = true;
        $(input).addClass('error');
        e.preventDefault();
      } else {
        $(input).removeClass('error');
      }
    });

    $('tbody#operations td select').each(function (i, select) {
      //get the adjustment type
      var adj_type = $(select).parent().parent().find('input[type="hidden"]').val();
      if ($(select).val().length === 0 && validEmptySelects.indexOf(adj_type) === -1) {
        error = true;
        $(select).addClass('error');
        e.preventDefault();
      } else {
        $(select).removeClass('error');
      }
    });
    if (error) {
      displayAlert(false, 'Some operation values must be entered');
      return false;
    }
    return true;
  });

  window.$selects = [];
  var options = {
    create: false,
    sortField: "text",
  };
  $selects = $('td.source_field select').selectize(options);
  $('div.lang-select-group div.selectize-control:not(:first-of-type)').hide();

  $("#content").on('click', '[intercom-tracked]', intercomTrack);

  //step-2 multishop check
  if ($('#multishop-tree ul li input:checked').length === 0) {
    //no shop has been selected
    $('#multishop-tree ul li:eq(0) input').click(); //click the default shop input to cause the cascade
  }
}

/**
* Display a bootstrap alert
*
* @param			boolean		success
* @param			string		messageString		string to display
*
* @return     nil
*/
function displayAlert(success, messageString) {
	var alertClass = (success) ? 'alert-success' : 'alert-danger';
	$('div.alert-wrap div.alert.'+alertClass).remove(); //remove alerts of same type
	var html = '';
	html += '<div class="alert '+alertClass+'" role="alert">'
	html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += messageString;
	html += '</div>';

	$('form.totalimportpro').prepend(html);
	$(document).scrollTop(0);
}

function addSub(el)
{
  sub = $(el).closest('.hori').children('td').children('.lang-select-group').first().clone();
  sub.attr('needsUpdate', 'true');
  $(el).before(sub);

  $('[needsUpdate="true"]').find('div.selectize-control').remove();
  $('[needsUpdate="true"]').find('select.selectized').removeClass('selectized').html(getOptions($selects[0].selectize.options)).selectize({
    create: false,
    sortField: "text",
  });
  $('[needsUpdate="true"]').removeAttr('needsUpdate').find('div.selectize-control:not(:first-of-type)').hide();
  $('.dropdown-toggle').dropdown();

  return false;
}

function addVert(el, multi)
{
  var newEl = $(el).gparent().clone().attr('needsUpdate', 'true');
  $(el).hide(); //hide the more button
  $(el).closest('.vert').after(newEl); //add new html to DOM

  if (multi) {
    $('[needsUpdate="true"]').find('.lang-select-group:not(:first-of-type)').remove();
  }

  $('[needsUpdate="true"]').find('div.selectize-control').remove();
  $('[needsUpdate="true"]').find('select.selectized').each(function () {
    var name = $(this).attr('name');
    var _name = /^(field_names\[category\])\[([0-9]{1,})\]\[([0-9]{1,})\]\[\]/g.exec(name);
    if (_name) {
      var newName = _name[1]+'['+_name[2]+']['+(parseInt(_name[3])+1)+'][]';
      $(this).attr('name', newName).removeClass('selectized').html(getOptions($selects[0].selectize.options)).selectize({
        create: false,
        sortField: "text",
      });
    } else {
      $(this).removeClass('selectized').html(getOptions($selects[0].selectize.options)).selectize({
        create: false,
        sortField: "text",
      });
    }
  });
  $('[needsUpdate="true"]').removeAttr('needsUpdate').find('div.lang-select-group div.selectize-control:not(:first-of-type)').hide();
  $('.dropdown-toggle').dropdown();
  return false;
}

function getOptions(options)
{
  var html = '<option value="">None</option>';
  for (i in options) {
    html += '<option value="'+options[i].value+'">'+options[i].text+'</option>';
  }
  return html;
}

function addCombo(el)
{
  var newEl = $(el).gparent().clone().attr('needsUpdate', 'true');
  $(el).hide(); //hide the more button
  $(el).closest('.combo').after(newEl); //add new html to DOM

  $('[needsUpdate="true"]').find('div.selectize-control').remove();
  $('[needsUpdate="true"]').find('select.selectized').removeClass('selectized').html(getOptions($selects[0].selectize.options)).selectize({
    create: false,
    sortField: "text",
  });
  $('[needsUpdate="true"]').removeAttr('needsUpdate').find('div.lang-select-group div.selectize-control:not(:first-of-type)').hide();

  return false;
}

function addCatVert(el, multi)
{
  newEl = '<tr class="vert';
  if (multi)
  {
    newEl += ' hori';
  }
  newEl += '">' + $(el).closest('.vert').html() + '</tr>';
  if (multi == true)
  {
    matches = newEl.match(/\]\[(\d+)\]\[\]/);
    count = parseInt(matches[1]);
    count = count + 1;
    newEl = newEl.replace(']['+(count-1).toString()+'][]', ']['+count.toString()+'][]');
  }
  $(el).hide();
  $(el).closest('.vert').after(newEl);
  return false;
}

function hideOtherLanguages(caller, lang_code, lang_name) {
  caller.closest('.dropdown').children('.dropdown-toggle').html(lang_name + '<span class="caret"></span>');
  caller.closest('.lang-select-group').children('.selectize-control').hide();
  caller.closest('.lang-select-group').children('.selectize-control.lang-' + lang_code).show();
}

function removeSelectBefore(el)
{
  $(el).prev().remove();
  $(el).remove();
}

function strip_tags (input, allowed) {
  allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(""); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
      commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

  if (input == null) return "";
  return input.replace(commentsAndPhpTags, "").replace(tags, function ($0, $1) {
    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : "";
  });
}

function buildSampleRow() {
  $.ajax({
    url: window.ajaxUrl,
    type: 'post',
    data: 'ajax=true&action=GetNextRow&nextRow=' + $('#nextRow').val(),
    dataType: 'json',
    success: function(json) {
      if (json) {
        $('#nextRow').val(parseInt($('#nextRow').val()) + 1);
        $('#sampleFields tbody').empty().append('<tr>');
        $('#sampleFields thead tr th').each(function() {
          tmp = strip_tags(json[$(this).text().trim()]);
          $('#sampleFields tbody tr').append('<td class="text-left">'+ ((tmp.length > 90) ? tmp.substr(0, 90) + '...' : tmp) + "</td>");
        });
      }
      else {
        $('#nextRow').val(0);
        buildSampleRow();
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {
      console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
}

$.fn.gparent = function () {
  return $(this).parent().parent();
};
