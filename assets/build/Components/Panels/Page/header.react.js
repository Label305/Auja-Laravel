/**
 * A page header
 *
 * - contents
 *
 * @jsx React.DOM
 */

define(['build/Components/Panels/Page/button.react'], function (Button) {
    return React.createClass({
        render: function () {
            var buttons = this.props.item.getButtons().map(function (button) {
                return (
                    Button({panel: this.props.panel, button: button})
                );
            }.bind(this));

            return (
                React.DOM.h2(null, this.props.item.text, " ", buttons)
            );
        }
    });

});