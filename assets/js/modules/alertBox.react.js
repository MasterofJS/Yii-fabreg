import React from 'react';
let component = null;

const TotalAlertBox = React.createClass({
    
    getInitialState () {
        return {
            message: null,
            type: null
        };
    },

    componentDidMount(){
        component = this;
        this.path = APP.props.location.pathname;
    },

    componentWillReceiveProps(nextProps){
        if (this.path !== APP.props.location.pathname) {
            this.setState({
                message: null
            });
            this.path = APP.props.location.pathname;
        }
    },

    render () {
        if (!this.state.message) {
            return null;
        }

        return (
            <div className="row wr_main_alert_">
                <div className="column text-center">
                    <div className={`alert-box main_alert_ ${ this.state.type || '' }`}>
                        {this.state.message}
                        <a href="#" onClick={this._closeAlert} className="close">&times;</a>
                    </div>
                </div>
            </div>
        );
    },

    _closeAlert(e) {
        e.preventDefault();

        this.setState({
            message: null
        });
    }

});

function showTotalAlertBox(message, type = null) {
    component.setState({message, type});
    setTimeout(function () {
        component.setState({message: null, type: null});
    }, 10000);
}

export default  {
    TotalAlertBox,
    showTotalAlertBox
};


