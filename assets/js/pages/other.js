import { API , ROOT } from '../modules/config';
import Page from '../modules/page';

const Privacy = React.createClass({
    getInitialState(){
        return {
            markup: ''
        }
    },
    componentWillMount(){
        var self = this;
        $.ajax({
            url: `${ ROOT }dist/pages/privacy.html`,
            success: function (result) {
                self.setState({
                    markup: result
                });
            }
        });
    },
    render () {

        return (
            <div className="row">
                <div className="wrap_text_ column inner_page_" dangerouslySetInnerHTML={{__html:this.state.markup}}></div>
            </div>
        )
    }
});


const Terms = React.createClass({
    getInitialState(){
        return {
            markup: ''
        }
    },
    componentWillMount(){
        var self = this;
        $.ajax({
            url: `${ ROOT }dist/pages/term.html`,
            success: function (result) {
                self.setState({
                    markup: result
                });
            }
        });
    },
    render () {

        return (
            <div className="row">
                <div className="wrap_text_ column inner_page_" dangerouslySetInnerHTML={{__html:this.state.markup}}></div>
            </div>
        )
    }
});

export {Privacy, Terms};

