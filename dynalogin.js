if (window.rcmail) {
  rcmail.addEventListener('init', function() {
    // create textbox
    var text = '';
    text += '<tr>';
    text += '<td class="title"><label for="dynalogin_code">'+rcmail.gettext('code', 'dynalogin')+'</label></td>';
    text += '<td class="input"><input name="_code" id="dynalogin_code" size="40" autocapitalize="off" autocomplete="off" type="password"></td>';
    text += '</tr>';

    // create textbox
    $('form > table > tbody:last').append(text);
    
  });

};
