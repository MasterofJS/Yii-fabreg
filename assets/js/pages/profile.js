import { API , afterTitle } from '../modules/config';
import PageNotFound from '../pages/pagenotfound';
import Header from '../components/profile/header.react';
import Page from '../modules/page';
import List from '../modules/listposts';
import * as router from 'react-router';
import fetchFirstList from  '../modules/fetchFirstList';
import Placeholder from '../modules/placehold';

export default React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            user: null,
            loaded: false
        };
    },

    componentWillMount(){
        $.ajax({
            url: `${ API }/users/${ this.props.params.name }`,
            data: {
                expand: 'profile,cover,avatar'
            },
            success: function (respond) {
                this.setState({
                    user: respond
                });

                document.title = respond.username + afterTitle;

            }.bind(this),
            error: function (error) {
                console.warn(error);
                if (error.status === 404) {
                    this.setState({
                        loaded: 404
                    });
                }
            }.bind(this)
        });
    },

    render () {

        if (this.state.loaded === 404) {
            return <PageNotFound/>
        }
        if (!this.state.user) {
            return null;
        }

        return (
            <div className="profile_page_">

                <Header
                    user={ this.state.user }
                    username={ this.props.params.name }
                />

                <ProfileBody
                    tab={this.props.params.tab}
                    name={this.props.params.name}
                    hide_upvotes={this.state.user.hide_upvotes}
                />
            </div>
        );
    }
});


const ProfileBody = React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            data: null,
            linkNextPage: null,
            perPage: 40
        };
    },

    componentDidMount () {
        const filter = this.props.tab || 'feed';
        fetchFirstList.call(this, `${ API }/users/${this.props.name}/${ filter }`);

    },

    componentWillReceiveProps(nextProps){
        const currentFilter = this.props.tab;
        const nextFilter = nextProps.tab;

        if (currentFilter !== nextFilter) {
            fetchFirstList.call(this, `${ API }/users/${this.props.name}/${ nextFilter || 'feed'}`);
        }
    },


    render () {
        const is_hide_upvotes = !!((this.props.hide_upvotes && (this.props.tab === 'likes')) && (this.props.name !== APP.state.user.username));
        return (
            <Page classes="profile_">
                {
                    <nav className="list_nav_">
                    <IndexLink
                        activeClassName="active"
                        className="button"
                        to={`/user/${this.props.name}`}>
                        <i className="icon-bullseye"></i>Geral</IndexLink>
                    <Link
                        activeClassName="active"
                        className="button"
                        to={`/user/${this.props.name}/posts`}>
                        <i className="icon-pencil"></i>Posts</Link>
                    <Link
                        activeClassName="active"
                        className="button"
                        to={`/user/${this.props.name}/likes`}>
                        <i className="icon-thumbs-up-alt"></i>Likes</Link>
                    <Link
                        activeClassName="active"
                        className="button"
                        to={`/user/${this.props.name}/comments`}>
                        <i className="icon-comment"></i>Comentários</Link>
                </nav>
                }
                <hr/>
                {
                    !this.state.data && !is_hide_upvotes &&
                    <Placeholder/>
                }
                {
                    this.state.data && this.state.data.length <= 0 && !is_hide_upvotes &&
                    <h3 className="page_title_ text-center">Não há mais posts</h3>
                }
                {
                    this.state.data && !is_hide_upvotes &&
                    <List { ...this.state }/>
                }
                {
                    is_hide_upvotes  &&
                    <div className="hide_upvotes_">
                        <h4 className="title_">Os likes desse usuário são secretos</h4>
                    </div>
                }
            </Page>
        );
    }
});

