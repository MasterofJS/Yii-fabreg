import { API } from '../../modules/config';
import Page from '../../modules/page.js';
import List from '../../modules/listposts';
import fetchFirstList from  '../../modules/fetchFirstList';

export default React.createClass({

    getInitialState () {
        return {
            data: null,
            linkNextPage: null,
            perPage: 40
        };
    },

    componentDidMount () {

        $('.total_wrap_').addClass('with_bg_');

        fetchFirstList.call(this, `${ API }/posts/fresh/trending`);

    },

    componentWillUnmount () {
        $('.total_wrap_').removeClass('with_bg_');
    },

    render () {
        const options = this.state;
        return (
            <Page classes="null">
                {
                    options.data &&
                    <List { ...options }/>
                }
            </Page>
        );
    }
});
