CKEDITOR.plugins.add( 'uinpdf', {
    icons: 'uinpdf',
    init: function( editor ) {
        editor.addCommand( 'uinpdf', new CKEDITOR.dialogCommand( 'uinpdfDialog' ) );
        editor.ui.addButton( 'uinpdf', {
            label: 'Select PDF Optional Text',
            command: 'uinpdf',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'uinpdfDialog', this.path + 'dialogs/uinpdf.js' );
    }
});