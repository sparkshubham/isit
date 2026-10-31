CKEDITOR.plugins.add( 'signaturedialog', {
    icons: 'signaturedialog',
    init: function( editor ) {
        editor.addCommand( 'signaturedialog', new CKEDITOR.dialogCommand( 'signatureDialog' ) );
        editor.ui.addButton( 'signaturedialog', {
            label: 'Select Signature',
            command: 'signaturedialog',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'signatureDialog', this.path + 'dialogs/signaturedialog.js' );
    }
});