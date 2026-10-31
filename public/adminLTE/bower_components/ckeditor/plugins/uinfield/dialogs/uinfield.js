CKEDITOR.dialog.add( 'uinfieldDialog', function( editor ) {
    return {
        title: 'Select Fields',
        minWidth: 400,
        minHeight: 200,
        width: 200,
        height: 75,
        contents: [
            {
                id: 'tab-basic',
                label: 'Select Fields',
                elements: [
                    {
                        type: 'select',
                        id: 'uinfield',                        
                        label: 'Select Fields',
                        items: documentFields,
                        validate: CKEDITOR.dialog.validate.notEmpty( "Please select a field." ),
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
            var value = dialog.getValueOf('tab-basic','uinfield');
            
            var uinfield = editor.document.createElement( 'uinfield' );
            
            uinfield.setText( "{{"+value+"}}" );

            // var id = dialog.getValueOf( 'abbr', 'id' );
            // if ( id )
            //     abbr.setAttribute( 'id', id );

            editor.insertElement( uinfield );
        }
    };
});