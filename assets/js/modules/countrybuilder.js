import { ROOT } from '../modules/config';

export default React.createClass({
    
    getInitialState () {
        return {
            list: [],
            country: this.props.country
        };
    },


    
    componentDidMount () {

        const that = this;

        this.request = $.ajax({
            url: `${ ROOT }data/countries.json`,
            success: function (respond) {
                var sorted;
                var counties = [];
                for (var key in respond) {
                    counties.push({val: key, country: respond[key]});
                }
                sorted = counties.sort(function (a, b) {
                    var textA = a.country.toUpperCase();
                    var textB = b.country.toUpperCase();
                    return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                });
                if (that.isMounted()) {
                    that.setState({
                        list: sorted
                    })
                }
            },
            error: function (error) {
                console.warn(error);
            }
        });

    },

    componentWillUnmount(){
        this.request.abort();
    },

    render () {
        return (
            <select
                ref="countries"
                {...this.props}
                value={ this.state.country }
                onChange={ this._onChange }
            >
                <option value="">País</option>
                {this.state.list.map(function (e, i) {
                    return <option value={e.val} key={i}>{e.country}</option>
                })}
            </select>
        );
    },

    _onChange(e){
        this.setState({
            country: e.target.value
        });
    }

});