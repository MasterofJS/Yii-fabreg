import { ROOT } from '../modules/config';
import {Notification, NotificationList, NotificationItem} from './notification';
import {TriggerModal} from '../modules/modal.react';
import CommentNewPost from './header/newPost.react';
import AutoForm from 'react-auto-form';
import Login from'./LoginForm';
import * as router from 'react-router';

const MobileMenu = React.createClass({

    getInitialState () {
        return {
            view: false
        };
    },


    render () {
        return (
            <li className="toggle-topbar menu-icon">
                <a href="#" data-dropdown="drop_search_menu" onClick={this._handlerMenu}>Menu{/* todo translate */}</a>

                {
                    this.state.view &&
                    <ul
                        className="f-dropdown content drop_search_menu"
                        id="drop_search_menu"
                        data-dropdown-content
                        aria-autoclose="false">
                    <li className="has-form">

                        <AutoForm id="searchFormMobile" onSubmit={this._handlerSubmit}>
                           {/* <input
                            type="text"
                            placeholder="Digite sua busca aqui…"/>*/}
                            <input value={this.state.searchValue}
                                   onChange={this._handlerSearch}
                                   onKeyPress={this._handlerSearchKey}
                                   type="text"
                                   name="search"
                                   placeholder="Digite sua busca aqui…"
                            />
                            <button
                                className="search_btn_ hidden-for-topbar"
                                type="submit"
                                /*onClick={this._handlerSubmit}*/>
                                <i className="icon-search"></i>
                            </button>
                        </AutoForm>

                    </li>
                    <li className="divider"></li>
                    <li><IndexLink activeClassName="active" onClick={ this._closeMenu }
                                   to="/">Top</IndexLink></li>
                    <li><Link activeClassName="active" onClick={ this._closeMenu } to="/trending">Em Alta</Link></li>
                    <li><Link activeClassName="active" onClick={ this._closeMenu } to="/fresh">Novos</Link></li>
                </ul>
                }
            </li>
        );
    },

    _handlerMenu(e){
        e.preventDefault();
        this.setState({
            view: !this.state.view
        })
    },

    _handlerSubmit(e, data){
        e.preventDefault();
        this.props.handleSearchSubmit(e, data, function () {

            this.setState({
                view: false
            });
        }.bind(this));
    },

    _closeMenu(){
        this.setState({
            view: false
        });
        scrollToTop();
    }
});

function clickSearchForm(e) {
    document.getElementById(e.currentTarget.getAttribute('form')).submit();
}

const AuthBlock = React.createClass({

    mixins: [router.History],

    render () {
        if (auth.loggedIn()) {
            return (

                <ul className="right">

                    <li>
                        <button type="button" onClick={this.props.handleSearchSubmit}
                                className="search_btn_ test_ visible-for-topbar todo">
                            <i className="icon-search"></i>
                        </button>
                    </li>

                    <MobileMenu handleSearchSubmit={this.props.handleSearchSubmit}/>

                    <Notification
                        notifyReady={this.props.notifyReady}
                    />

                    <li className="user_">
                        <a
                            href="#"
                            data-dropdown="drop_user_menu"
                            style={{
                                backgroundImage: `url(${this.props.user !== 'guest' && this.props.user._links && this.props.user._links.avatar ? this.props.user._links.avatar.href : null})`
                            }}
                        ></a>
                        <ul
                            className="f-dropdown"
                            id="drop_user_menu"
                            data-dropdown-content
                        >
                            <li className="visible-for-menu-only">

                                <TriggerModal
                                    className="button"
                                    id="modal_new_post"
                                    width={624}
                                >
                                    Postar!
                                <CommentNewPost history={this.history}/>
                            </TriggerModal>
                            </li>
                            <li>
                                <Link
                                    title={this.props.user.username}
                                    to={'/user/' + this.props.user.username}
                                >Meu Perfil</Link>
                            </li>
                            <li>
                                <Link to="/settings/account">Configurações</Link>
                            </li>
                            <li>
                                <a href="#" onClick={auth.logout}>Sair</a>
                            </li>
                        </ul>
                    </li>

                    <li className="login hidden-for-menu-only">

                        <TriggerModal
                            id="modal_new_post"
                            width={624}
                        >
                            <span>Postar!</span>
                            <CommentNewPost history={this.history}/>
                        </TriggerModal>

                    </li>

                </ul>

            );
        } else {

            return (
                <ul className="right">
                    <li>
                        <button
                            type="button"
                            onClick={this.props.handleSearchSubmit}
                            className="search_btn_ test_ visible-for-topbar"
                        >
                            <i className="icon-search"></i>
                        </button>
                    </li>

                    <MobileMenu handleSearchSubmit={this.props.handleSearchSubmit}/>

                    <li className="divider"></li>
                    <li className="hidden-for-menu-only"><Link to="/signup">Registre-se</Link></li>
                    <li className="login ">
                        <a
                            href="#"
                            data-dropdown="drop_login"
                            onClick={this._handleClickLogin}
                        >Entrar</a>
                    </li>
                </ul>
            );
        }
    },

    onUpdate (val) {
        this.setState({
            auth: val
        });
    }

});

const ListHelper = React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            query: null
            //list: null
        };
    },


    //componentDidMount() {

    //this.loadList(this.props.data)
    //},

    //componentWillReceiveProps(nextProps) {
    //if (this.props === nextProps) {
    //    return;
    //}
    //this.loadList(nextProps.data)
    //},

    //loadList (data) {
    //const that = this;
    //if (data === null || data.length < 3) {
    //    this.setState({
    //        list: null
    //    });
    //    return;
    //}
    //
    //if (Date.now() - this._timer >= 2000) {
    //    this._timer = Date.now();
    //
    //    $.ajax({
    //        url: 'data/search_results_helper.json', // todo
    //        success: function (respond) {
    //            that.setState({
    //                list: respond
    //            });
    //        },
    //        error: function (error) {
    //            console.warn(error);
    //        }
    //    });
    //}
    //},

    _timer: Date.now(),

    render () {
        const that = this;
        if (!this.state.list) {
            return null;
        }
        return (
            <ul className="list_">
                {this.state.list.map(function (item, i) {
                    return <li key={i} onClick={that._handleClickItem}>{item}</li>;
                })}
            </ul>
        );
    },

    _handleClickItem(e){
        e.preventDefault();
        this.history.replaceState(null, '/search', {query: e.target.innerText});
        this.setState({
            list: null
        });
    }

});

export default React.createClass({

    getInitialState () {
        return {
            searchValue: null,
            notify_ready: false
        }
    },

    mixins: [router.History],

    componentDidMount(){
        global.main_nav = this;
        $(document).foundation('dropdown', 'reflow');
    },

    render () {
        return (
            <div>
                <div className="contain-to-grid fixed main_navigation_">
                    <nav className="top-bar" role="navigation">
                        <ul className="title-area">
                            <li className="name">
                                <Link to="/"><img src={`${ ROOT }dist/images/logo.svg`} alt="Unicorno logo"/></Link>
                            </li>
                        </ul>

                        <div className="top-bar-section">
                            <ul className="left">
                                <li className="has-form search_form_wr_">
                                    <AutoForm id="searchForm" onSubmit={this._handleSearchSubmit}>
                                        <input value={this.state.searchValue}
                                               onChange={this._handlerSearch}
                                               onKeyPress={this._handlerSearchKey}
                                               type="text"
                                               name="search"
                                               ref="search"
                                               placeholder="Digite sua busca aqui…"
                                        />
                                        <button type="submit" className="search_btn_ hidden-for-topbar">
                                            <i className="icon-search"></i>
                                        </button>
                                    </AutoForm>
                                    {/*this.state.searchValue && <ListHelper data={this.state.searchValue}/>*/}
                                </li>
                                <li className="divider"></li>
                                <li><IndexLink onClick={scrollToTop} activeClassName="active"
                                               to="/">Top</IndexLink></li>
                                <li className="divider"></li>
                                <li><Link onClick={scrollToTop} activeClassName="active"
                                          to="/trending">Em Alta</Link></li>
                                <li className="divider"></li>
                                <li><Link onClick={scrollToTop} activeClassName="active" to="/fresh">Novos</Link></li>
                                <li className="divider"></li>
                            </ul>

                        </div>
                        <div className="top-bar-section mobile_only">
                            <AuthBlock
                                user={this.props.user}
                                handleSearchSubmit={this._handleSearchSubmit}
                                notifyReady={this.notifyReady}
                            />
                        </div>
                    </nav>
                    {
                        !auth.loggedIn() &&
                        <div id="drop_login"
                             data-dropdown-content
                             className="f-dropdown content"
                             aria-hidden="true"
                             aria-autoclose="false"
                             tabIndex="-1">
                            <Login />
                        </div>
                    }

                    {
                        this.state.notify_ready &&
                        <NotificationList />
                    }

                </div>
            </div>
        );
    },

    onUpdate (val) {
        this.setState({login: val});
    },

    _handlerSearch(e){
        const value = e.target.value;
        this.setState({
            searchValue: value
        });
    },

    _handleSearchSubmit(e, data, cb) {
        var val = typeof data.search === 'string' ? data.search : this.refs.search.value;
        e.preventDefault();
        if (val.length < 2) {
            return;
        }
        this.history.replaceState(null, '/search', {query: val});
        this.setState({
            searchValue: null
        });

        if (typeof cb === 'function') {
            cb();
        }
    },

    notifyReady(b){
        this.setState({
            notify_ready: b
        })
    }

});

function scrollToTop() {
    $('html,body').animate({
        scrollTop: 0
    }, 100);
}