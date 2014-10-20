/**
 * The main menu on the left
 * 
 * @jsx React.DOM
 * @todo new name!
 */
define([], function() {
    
    var Item = React.createClass({displayName: 'Item',
        handleClick: function() {
            flux.actions.click(this.props.item.target);
        },
        render: function() {            
            var className = "auja-bg-main";
            
            if(this.props.item.icon) {
                className += " icon ion-" + this.props.item.icon;
            }
            
            return (
                React.DOM.li({className: className, title: this.props.item.title, onClick: this.handleClick}, 
                    React.DOM.span(null, this.props.item.title)
                )
                );
        }
    });

    return React.createClass({
        render: function() {
            var menu = this.props.auja.menu.map(function(item) {
                return (
                    Item({key: item.target, auja: this.props.auja, item: item})
                    );
            }.bind(this));
            
            return (
                React.DOM.ul({id: "main-menu"}, 
                    menu
                )
                );
        }
    });

});