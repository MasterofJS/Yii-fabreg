import { API } from '../../modules/config';
import Page from '../../modules/page';
import List from '../../modules/listposts';
import fetchFirstList from  '../../modules/fetchFirstList';
import Placeholder from '../../modules/placehold';

export default React.createClass({

    getInitialState () {
        return {
            data: null,
            linkNextPage: null,
            perPage: 40
        };
    },

    componentDidMount () {
        const filter = this.props.route.path || 'hot';

        $('.total_wrap_').addClass('with_bg_');

        this.request = fetchFirstList.call(this, `${ API }/posts/${ filter }`);

    },

    componentWillReceiveProps(nextProps){
        //const currentFilter = this.props.route.path;
        const nextFilter = nextProps.route.path;
        //console.log(11111,nextProps);
        //if (currentFilter !== nextFilter) {
        this.request = fetchFirstList.call(this, `${ API }/posts/${ nextFilter || 'hot'}`);
        //}
    },

    componentWillUnmount () {
        $('.total_wrap_').removeClass('with_bg_');

        this.request.abort();
    },

    render () {
        return (
            <Page classes="null">
                {
                    !this.state.data &&
                    <Placeholder/>

                }
                {
                    this.state.data &&
                    <List { ...this.state }/>
                }
            </Page>
        );
    }
});
