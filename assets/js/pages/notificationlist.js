import { PROD, API } from '../modules/config';
import Page from '../modules/page';
import moment from 'moment';
import 'moment/locale/pt-br.js';

import { buildMessage, getIcon, getFromNow } from '../services/buildNotificationMessage';

/*
 const Item = React.createClass({
 render () {
 let [item,key] = [this.props.data, this.props.key];
 let [name1,name2] = item.names;
 let message;
 if (item.amount > 0) {
 message = ' and ' + item.amount + ' other’s ' + (item.like ? 'likes' : 'comments') + ' your post';
 } else {
 message = ' ' + (item.like ? 'likes' : 'comments') + ' your post';

 }

 return (
 <li key={key}>
 <Link to={`/posts/${item.link}`}>
 <i className={item.like? "icon-thumbs-up" : "icon-comment"}></i>
 <p>
 <strong>{name1}</strong>
 {name2 ? ' and ' : null}
 {name2 ? (<strong>{name2}</strong>) : null}
 {message}
 </p>
 <span className={item.like? null : "comment"}>
 {getFromNow(item.date)}</span>
 </Link>
 </li>
 );
 }
 });*/

const Item = React.createClass({
    render () {
        const item = this.props;
        let link = `/posts/${ item.post.id }`;
        return (
            <li className={ item.is_read ? null : 'unread_' }>
                <Link to={ link }>
                    <img src={ item.post._links.photo.href } alt={ item.post.description }/>
                </Link>
                <Link to={ link }>
                    {
                        buildMessage(item)
                    }
                    <span>

                        {
                            getIcon(item.type)
                        }

                        {
                            getFromNow(item.timestamp)
                        }

                    </span>
                </Link>
            </li>
        );
    }
});


export default React.createClass({

    getInitialState () {
        return {
            items: [],
            loading: false
        };
    },
    componentWillMount () {
        //let self = this;
        //$.getJSON(`${ API }/notifications`, function (result) {
        //    if (!result || !result.length) {
        //        return;
        //    }
        //    self.setState({
        //        items: result,
        //        loading: true
        //    });
        //});


        let url = PROD ? `${ API }/notifications` : '/data/notifications.json';
        $.getJSON(url, {
            expand: 'actors',
            'per-page': 50
        }, function (result, t, requst) {

            this.setState({
                items: result,
                loading: true
            });


        }.bind(this));
    },
    render () {

        return (
            <Page classes="notif_">
                <h4 className="page_title_">Notificações</h4>
                <hr/>
                {
                    (this.state.loading && this.state.items.length <= 0) &&
                    <h5 className="unstyled_">Sem atividade</h5>
                }
                <ul className="list_">
                    {
                        this.state.loading &&
                        this.state.items.map(function item(data, i) {
                            return (
                                <Item key={i} { ...data }/>
                            );
                        })
                    }
                </ul>
            </Page>
        );
    }
});

//no notifications
