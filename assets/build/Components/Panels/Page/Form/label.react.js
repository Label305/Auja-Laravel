/**
 * A label as in <label>, properties:
 *
 * - name
 * - item
 *
 * @todo Add validation
 *
 * @jsx React.DOM
 */

define([], function () {
    return React.createClass({
        render: function () {
            
            //Extract the validation message
            var validation = '';
            if(this.props.item.validationMessage != null) {
                validation = (
                    React.DOM.span({className: "validation-message auja-color-alert"}, this.props.item.validationMessage)
                    );
            }
            
            return (
                React.DOM.label(null, 
                React.DOM.span(null, this.props.name), 
                validation
                )
                );
        }
    });

});