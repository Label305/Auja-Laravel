/**
 * A submit button, properties:
 *
 * - name
 * - .. any other allowed by input
 *
 * @jsx React.DOM
 */
define([], function () {

    return React.createClass({
        render: function () {
            var attributes = this.props.item.getAttributes();
            attributes.className = 'button auja-bg-main';
            
            return (
                React.DOM.div(null, 
                React.DOM.input(attributes)
                )
            );
        }
    });
});