import React from 'react';

const Modal = React.createClass({

    getInitialState () {
        return {
            show: true
        };
    },

    componentWillReceiveProps(){
        this.setState({
            show: true
        })

    },

    render () {
        var Comp;
        if (!this.state.show) {
            return null;
        }
        Comp = React.cloneElement(this.props.children, {handleClose: this.closeModal});
        return (
            <div id={this.props.id}>
                <div className="reveal-modal-bg" style={{'display': 'block'}} onClick={this.closeModal}></div>
                <div className="reveal-modal open" data-reveal="" aria-labelledby="firstModalTitle" aria-hidden="false"
                     role="dialog" tabIndex="0"
                     style={{
                     display: 'block',
                     width: `${this.props.width}px`,
                     opacity: 1,
                     visibility: 'visible',
                     top: $(window).scrollTop()
                     }}>
                    {Comp}
                    <a className="close-reveal-modal" onClick={this.closeModal}>&#215;</a>
                </div>

            </div>
        );
    },

    closeModal(){
        this.setState({
            show: false
        })
    }

});

const TriggerModal = React.createClass({

    render () {
        return (
            <a href="#" className={this.props.className} onClick={this.handleClick}>{this.props.children[0]}</a>
        );
    },

    getDefaultProps(){
        return {
            onClick: function () {
                return true;
            }
        }
    },

    handleClick(e) {
        e.preventDefault();

        if (!this.props.onClick()) {
            return;
        }

        ReactDOM.render(
            <Modal id={this.props.id} width={this.props.width}>
                {this.props.children[1]}
            </Modal>,
            document.getElementById('modal_base')
        )
    }

});


export {Modal,TriggerModal};
