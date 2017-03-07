import React from 'react';
import Account  from '../components/settings/account';
import Password  from '../components/settings/password';
import Profile  from '../components/settings/profile';
import Delete  from '../components/settings/delete';


const Menu = React.createClass({
    render () {
        return (
            <ul className="menu_ no-bullet">
                <li>
                    <Link activeClassName="active" className="button expand" to={'/settings/account'}>
                        Configurações de conta
                    </Link>
                </li>
                <li>
                    <Link activeClassName="active" className="button expand" to={'/settings/password'}>Senha</Link>
                </li>
                <li>
                    <Link activeClassName="active" className="button expand" to={'/settings/profile'}>Perfil</Link>
                </li>
            </ul>
        );
    }
});

const Settings = React.createClass({

    render () {

        return (
            <div className="row settings_">
                <nav className="aside_nav_ medium-3 column">
                    <Menu/>
                </nav>
                <section className="medium-9 column">
                    {this.props.children && React.cloneElement(this.props.children, {
                        user: this.props.user
                    })}
                </section>
            </div>
        );
    }
});


module.exports = {
    Settings: Settings,
    Account: Account,
    Password: Password,
    Profile: Profile,
    Delete: Delete
};