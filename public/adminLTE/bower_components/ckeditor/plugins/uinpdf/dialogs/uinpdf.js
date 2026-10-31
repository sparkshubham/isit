CKEDITOR.dialog.add( 'uinpdfDialog', function( editor ) {
    return {
        title: 'Select PDF Optional Text',
        minWidth: 400,
        minHeight: 200,
        width: 200,
        height: 75,
        contents: [
            {
                id: 'tab-basic',
                label: 'Select PDF Optional Text',
                elements: [
                    {
                        type: 'select',
                        id: 'uinpdf',
                        label: 'Select PDF Optional Text',
                        items: documentPdfTexts,
                        validate: CKEDITOR.dialog.validate.notEmpty( "Please select an optional text." ),
                        // default: 'Football',
                        onChange: function( api ) {
                            // this = CKEDITOR.ui.dialog.select
                            // alert( 'Current value: ' + this.getValue() );
                        }
                    },

                ]
            },
        ],
        onOk: function() {
            
            var dialog = this;
            var value = dialog.getValueOf('tab-basic','uinpdf');
            
            var uinpdf = editor.document.createElement( 'uinpdf' );
            uinpdf.setText( "{{"+value+"}}" );
            editor.insertElement( uinpdf );
        }
    };
});