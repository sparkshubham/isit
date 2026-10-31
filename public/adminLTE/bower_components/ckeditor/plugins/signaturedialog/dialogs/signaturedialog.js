CKEDITOR.dialog.add( 'signatureDialog', function( editor ) {
    return {
        title: 'Select Signature',
        minWidth: 400,
        minHeight: 200,
        width: 200,
        height: 75,
        contents: [
            {
                id: 'tab-basic',
                label: 'Select Signature',
                elements: [
                    {
                        type: 'select',
                        id: 'signaturedialog',                        
                        label: 'Select Signature',
                        items: documentSignature,
                        validate: CKEDITOR.dialog.validate.notEmpty( "Please select a signature." ),
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
            var value = dialog.getValueOf('tab-basic','signaturedialog');
            
            var signaturedialog = editor.document.createElement( 'signaturedialog' );
            
            signaturedialog.setText( "{{"+value+"}}" );

            // var id = dialog.getValueOf( 'abbr', 'id' );
            // if ( id )
            //     abbr.setAttribute( 'id', id );

            editor.insertElement( signaturedialog );
        }
    };
});