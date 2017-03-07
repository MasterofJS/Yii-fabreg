import Login from '../components/LoginForm';
import * as router from 'react-router';

export default React.createClass({

    mixins: [router.History],

    componentWillMount: redirect,

    componentWillReceiveProps: redirect,

    render () {
        return (
            <div className="row">
                <div className="columns">
                    <div className="wrap_login_page_">

                        <Login notDropdown={true} sender={this.props.location.state}/>
                    </div>
                </div>
            </div>
        );
    }
});

function redirect() {

    if (auth.loggedIn()) {
        this.props.history.replaceState(null, '/');
    }

}
