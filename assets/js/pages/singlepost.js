import { API } from '../modules/config';
import Placehold from '../modules/placehold';
import Page from '../modules/page';
import Post from '../modules/single/Post';
import CommentBox from '../modules/single/CommentBox';
import PageNotFound from '../pages/pagenotfound';


export default React.createClass({
    getInitialState(){
        return {
            loaded: false,
            data: null,
            comments: null
        }
    },

    componentDidMount () {
        this.fetchData()
    },

    componentDidUpdate (prevProps) {
        let oldId = prevProps.params.id;
        let newId = this.props.params.id;
        if (newId !== oldId) {
            this.fetchData()
        }
    },

    componentWillReceiveProps(nextProps){
        if (nextProps.location.hash.substr(1) === 'reload') {
            this.setState({
                data: null,
                loaded: false
            }, function () {
                this.fetchData()
            }.bind(this));
        }
    },

    fetchData () {
        const self = this;
        self.setState({
            loaded: false
        });
        $.getJSON(`${ API }/posts/${this.props.params.id}`, function (respond) {

            if (respond.is_nsfw && (!APP.state.user || APP.state.user === 'guest')) {
                debugger;
                APP.props.history.replaceState(null, '/login', {'ref': APP.props.location.pathname});
                return;
            }
            self.setState({
                loaded: true,
                data: respond
            });
        }).fail(function (e) {
            if (e.status === 404) {
                self.setState({
                    loaded: 404
                })
            }
        });
    },

    render () {
        const data = this.state.data;
        if (this.state.loaded === 404) {
            return <PageNotFound/>
        }
        return (
            <Page>
                {
                    this.state.loaded ?
                        <Post id={this.props.params.id} data={this.state.data}/> :
                        <Placehold/>}
                {
                    this.state.loaded &&
                    <CommentBox
                        {...data}
                        id={this.props.params.id}
                        amount={this.state.data.comments}
                        user={this.props.user}
                    />
                }
            </Page>
        );
    }
});

