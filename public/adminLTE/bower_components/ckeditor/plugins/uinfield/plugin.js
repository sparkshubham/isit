CKEDITOR.plugins.add( 'uinfield', {
    icons: 'uinfield',
    init: function( editor ) {
        editor.addCommand( 'uinfield', new CKEDITOR.dialogCommand( 'uinfieldDialog' ) );
        editor.ui.addButton( 'uinfield', {
            label: 'Select Fields',
            command: 'uinfield',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'uinfieldDialog', this.path + 'dialogs/uinfield.js' );
    }
});