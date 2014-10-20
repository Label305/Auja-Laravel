/**
 * @jsx React.DOM
 */
define([], function () {

    return React.createClass({
        render: function () {
            //Name of the user
            var user = '';
            if (this.props.auja.user) {
                user = (
                    React.DOM.div({className: "auja-color-main", id: "user"}, this.props.auja.user.name)
                    );
            }

            //Buttons, e.g. logout
            var buttons = '';
            if (this.props.auja.buttons) {
                buttons = this.props.auja.buttons.map(function (button) {
                    return (
                        React.DOM.a({className: "auja-bg-main button", key: button.target, href: button.target}, button.text)
                        );
                });
            }
            return (
                React.DOM.header(null, 
                    React.DOM.h1({className: "auja-color-main"}, this.props.auja.title), 
                    React.DOM.div({id: "buttons"}, buttons), 
                    user
                )
                );

        }
    });

});
                        