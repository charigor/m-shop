// The editor creator to use.
import { ClassicEditor as ClassicEditorBase } from '@ckeditor/ckeditor5-editor-classic';
import { SimpleUploadAdapter } from '@ckeditor/ckeditor5-upload';
import { UploadAdapter } from '@ckeditor/ckeditor5-adapter-ckfinder';
import { Autoformat } from '@ckeditor/ckeditor5-autoformat';
import { Bold, Italic } from '@ckeditor/ckeditor5-basic-styles';
import { BlockQuote } from '@ckeditor/ckeditor5-block-quote';
import { EasyImage } from '@ckeditor/ckeditor5-easy-image';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Heading } from '@ckeditor/ckeditor5-heading';
import { Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload, ImageResizeEditing, ImageResizeHandles,ImageResize } from '@ckeditor/ckeditor5-image';
import { Link } from '@ckeditor/ckeditor5-link';
import { List } from '@ckeditor/ckeditor5-list';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';

import { Alignment } from '@ckeditor/ckeditor5-alignment'; // <--- ADDED

export default class ClassicEditor extends ClassicEditorBase {}

// Plugins to include in the build.
ClassicEditor.builtinPlugins = [
    SimpleUploadAdapter,
    Essentials,
    UploadAdapter,
    Autoformat,
    Bold,
    Italic,
    BlockQuote,
    // EasyImage,

    Heading,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    ImageResize,
    ImageResizeEditing,
    ImageResizeHandles,
    Link,
    List,
    Paragraph,
    Alignment                                                            // <--- ADDED
];

// Editor configuration.
ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'heading',
            '|',
            'alignment',                                                 // <--- ADDED
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'uploadImage',
            'blockQuote',
            'undo',
            'redo'
        ]
    },
    image: {
        resizeOptions: [
            {
                name: 'resizeImage:original',
                value: null,
                label: 'Original'
            },
            {
                name: 'resizeImage:40',
                value: '40',
                label: '40%'
            },
            {
                name: 'resizeImage:60',
                value: '60',
                label: '60%'
            }
        ],
        toolbar: [
            'imageStyle:inline',
            'imageStyle:block',
            'imageStyle:side',
            'resizeImage',
            '|',
            'toggleImageCaption',
            'imageTextAlternative'
        ]
    },


    // This value must be kept in sync with the language defined in webpack.config.js.
    language: 'en'
};
