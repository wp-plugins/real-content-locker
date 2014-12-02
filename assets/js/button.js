(function() {
  tinymce.PluginManager.add('realcontentlocker', function( editor, url ) {
    editor.addButton( 'realcontentlocker', {
      icon: 'icon realcontentlocker-icon',
      title: "Real Content Locker",
      type: 'button',
      onclick: function() {
        editor.windowManager.open( {
          title: 'Insert title',
          body: [
            {
              type: 'textbox',
              name: 'multilineName',
              label: 'Title content',
              value: '',
              multiline: true,
              minWidth: 300,
              minHeight: 100
            } 
          ],
          onsubmit: function( e ) {
            selection = tinyMCE.activeEditor.selection.getContent();
            editor.insertContent( '[realcontentlocker title="'+e.data.multilineName+'"]'+selection+'[/realcontentlocker]');

          }
        });
      }
    });
  });
})();