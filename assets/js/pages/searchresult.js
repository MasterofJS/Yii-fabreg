import { API } from '../modules/config';
import Page from '../modules/page';
import List from '../modules/listposts';
import Placeholder from '../modules/placehold';
import fetchFirstList from  '../modules/fetchFirstList';
require('es6-object-assign').polyfill();


export default React.createClass({

    getInitialState () {
        return {
            data: null,
            linkNextPage: null,
            perPage: 40,
            count: -1
        };
    },

    componentDidMount () {

        $('.total_wrap_').addClass('with_bg_');

        fetchFirstList.call(this, `${ API }/posts/search`, 'search',
            Object.assign(this.props.location.query, {'per-page': this.state.perPage}));

    },

    componentWillReceiveProps(nextProps){
        const current = this.props.location.query && this.props.location.query.query;
        const next = nextProps.location.query && nextProps.location.query.query;

        this.setState({
            data: null,
            count: -1
        }, function () {
            if ((next && current)/* && (current !== next)*/) {
                fetchFirstList.call(this, `${ API }/posts/search`, 'search', Object.assign(this.props.location.query, {'per-page': this.state.perPage}));
            }
        }.bind(this));

    },

    render () {
        return (
            <Page>
                <h1 className="page_title_">{this.props.location.query.query}</h1>
                {
                    (+this.state.count >= 0) &&
                    `${this.state.count} resultados encontrados em sua busca`
                }
                <hr/>

                {
                    this.state.data &&
                    <List { ...this.state }/>
                }

                {
                    (+this.state.count === 0) &&
                    <div className="no_results_ text-center">
                        <h2 className="title_">Nenhum resultado encontrado</h2>
                        <h3 className="subtitle_">Sem resultados em sua pesquisa</h3>
                    </div>
                }
            </Page>
        );
    }

});




