import moment from 'moment';
import 'moment/locale/pt-br.js';

export default React.createClass({
    render () {
        return (
            <header className="head_ clearfix">
                <Link target="_blank" to={this.props.link}><h1 className="title_">{this.props.title}</h1></Link>
                <Link target="_blank" to={this.props.link}
                      className="date">{moment.unix(/*-1 * (new Date()).getTimezoneOffset() * 60 +*/ this.props.date).format("LL")}</Link>
            </header>
        );
    }
});