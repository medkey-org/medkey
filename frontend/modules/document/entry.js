import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';

// Or using the CommonJS version:
// const DecoupledEditor = require( '@ckeditor/ckeditor5-build-decoupled-document' );

window.registerDocumentEdit = function () {
    DecoupledEditor
        .create( document.querySelector( '#editor' ) )
        .then( editor => {
            const toolbarContainer = document.querySelector( '#toolbar-container' );

            toolbarContainer.appendChild( editor.ui.view.toolbar.element );
        } )
        .catch( error => {
            console.error( error );
        } );
};