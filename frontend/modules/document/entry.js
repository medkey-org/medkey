import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';

// Or using the CommonJS version:
// const DecoupledEditor = require( '@ckeditor/ckeditor5-build-decoupled-document' );

DecoupledEditor
    .create( '<h2>Hello world!</h2>', {
        toolbarContainer: document.querySelector( '.toolbar-container' ),
        editableContainer: document.querySelector( '.editable-container' )
    } )
    .then( editor => {
        window.editor = editor;
    } )
    .catch( err => {
        console.error( err.stack );
    } );