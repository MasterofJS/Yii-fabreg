import {API} from '../modules/config';
import {FacebookButton, GoogleButton} from '../components/signWithSocial.react';
import * as router from 'react-router';
export default React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            error: false
        }
    },

    componentDidMount(){

        $(document).foundation('abide', 'reflow');
        //$(this.refs['email_field']).focus();
    },

    render () {

        if (auth.loggedIn()) {
            return null;
        }

        return (
            <form className="login_form_" data-abide onSubmit={this.handleSubmit}>
                <div className="row_ collapse">
                    <div className="social-row_">
                        <FacebookButton className="item button facebook" closeDropdown={this.closeDropdown}>
                            <i className="icon-facebook"></i>
                            <span>LOGIN COM FACEBOOK</span>
                        </FacebookButton>
                        <GoogleButton className="item button gplus" closeDropdown={this.closeDropdown}>
                            <i className="icon-gplus"></i>
                            <span>LOGIN COM GOOGLE</span>
                        </GoogleButton>
                    </div>
                    <p className="text-center">Ou login com</p>
                </div>

                <div className="row_ collapse merge_fields_">
                    <div
                        className="small-12 columns"
                        ref="email"
                    >
                        <input
                            type="email"
                            ref="email_field"
                            required
                            placeholder="Email"
                        />
                        <small className="error">Precisamos de um endereço de email</small>
                    </div>
                    <div className="small-2 columns">
                        <span className="postfix"><i className="icon-email"></i></span>
                    </div>
                </div>
                <div className="row_ collapse merge_fields_">
                    <div className="small-12 columns" ref="password">
                        <input type="password"
                               ref="password_field"
                               id={`password${Date.now()}`}
                               required pattern="[a-zA-Z]+"
                               placeholder="Senha"/>
                        <small className="error">Sua senha precisa ter letras e números, mínimo de 6 caracteres</small>
                    </div>
                    <div className="small-2 columns">
                        <span className="postfix"><i className="icon-lock"></i></span>
                    </div>
                    <div className="small-12 columns">
                        <a href="#" onClick={this._handlerForgot} className="right"
                           tabIndex="-1">Esqueci minha senha</a>
                    </div>
                </div>

                <div className="row_ collapse">
                    <div className="columns">
                        <input type="submit" className="button expand" value="Entrar"/>
                    </div>
                </div>
                <div className="row_ collapse text-center">
                    <Link
                        to={`/signup`}
                        query={matchRef.call(this)}
                        className="orange_c_ bold_"
                        onClick={this.closeDropdown}>Registre-se</Link>
                </div>
            </form>
        );

    },

    handleSubmit (event) {
        let email = this.refs.email_field.value;
        let password = this.refs.password_field.value;
        const that = this;

        event.preventDefault();

        $.ajax({
            url: `${API}/auth/login`,
            type: 'post',
            dataType: 'JSON',
            data: {
                email: email, password: password
            },
            success (respond) {
                if (Array.isArray(respond)) {

                    respond.forEach((r)=> {
                        $(ReactDOM.findDOMNode(that.refs[r.field]))
                            .addClass('error')
                            .find('.error')
                            .text(r.message);
                    });

                    return;
                }

                auth.setUser(respond, APP.props.location.query.ref ? APP.props.location.query.ref : null);

                if (that.props.notDropdown) {
                    if (that.props.sender) {
                        that.history.replaceState(null, that.props.sender.path)
                    }
                }


            },
            error: function (error) {
                console.warn(error);
            }
        });
    },

    _handlerForgot(){

        this.history.replaceState(null, '/forgot', {email: this.refs.email.value});
        if (!this.props.notDropdown) {
            $('.total_wrap_').trigger('click');
        }

    },

    closeDropdown (e) {
        if (e) {
            e.stopPropagation();
        }
        $('.total_wrap_').trigger('click');
    }
})



function matchRef() {
    if (APP.props.location.query.ref) {
        return {ref: APP.props.location.query.ref};
    } else {
        return {};
    }
}
