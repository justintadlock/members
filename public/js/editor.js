
let el                = wp.element.createElement;
let registerBlockType = wp.blocks.registerBlockType;
let ServerSideRender  = wp.components.ServerSideRender;
let TextControl       = wp.components.TextControl;
let InspectorControls = wp.components.InspectorControls;
let RichText          = wp.editor.RichText;

registerBlockType( 'members/logged-in', {

	title : 'Members: Logged In',
	icon  : 'lock',
	category : 'widgets',

	attributes: {
            content: {
                type: 'string',
                source: 'html',
                selector: 'p',
            }
        },

        edit: function( props ) {
            var content = props.attributes.content;

            function onChangeContent( newContent ) {
                props.setAttributes( { content: newContent } );
            }

            return el(
                RichText,
                {
                    tagName: 'p',
                    className: props.className,
                    onChange: onChangeContent,
                    value: content,
                }
            );
        },

        save: function( props ) {
            var content = props.attributes.content;

            return el( RichText.Content, {
                tagName: 'p',
                className: props.className,
                value: content
            } );
        },
} );
