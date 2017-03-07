import {API, PROD} from '../modules/config';
//noinspection ES6UnusedImports
import {buildMessage, getIcon, getFromNow} from '../services/buildNotificationMessage';
const LIST = {};


function getUnreadNotifications() {

    function getUnread() {
        $.get(`${ API }/notifications/get-unread`, function (respond) {

            if (this.isMounted()) {
                this.setState({
                    total_count: respond
                }, function () {
                    if (+this.state.total_count !== +respond) {
                        LIST.component.loadItems();
                    }
                });
            }
        }.bind(this));
    }

    getUnread.call(this);

    return setInterval(getUnread.bind(this), 30000);
}

function Notify() {

    let Component = null;
    return {
        init() {
            Component = this;
        },

        update(data) {
            if (Component) {
                Component.setState(data);
            }
        },

        destroy(){
            Component = null;
        }
    }
}

const NotifyComponent = new Notify();

const Notification = React.createClass({

    getInitialState () {
        return {
            total_count: 0
        };
    },

    interval: null,

    componentDidMount(){
        NotifyComponent.init.call(this);
        this.props.notifyReady(true);

        this.interval = getUnreadNotifications.call(this);
    },

    componentWillUnmount(){
        NotifyComponent.destroy();
        this.props.notifyReady(false);

        clearInterval(this.interval);
    },

    render () {
        return (
            <li className="notification_">
                <a
                    href="#"
                    ref="notification_menu"
                    data-dropdown="drop_notification_menu"
                    onClick={this._readAll}
                >
                    <i className="icon-bell-alt"></i>
                    {
                        (+this.state.total_count > 0) &&
                        <span className="amount_">{ this.state.total_count }</span>
                    }
                </a>
            </li>
        );
    },
    _readAll(){

        if ($(this.refs.notification_menu).attr('aria-expanded') === 'true') {
            return;
        }

        LIST.component.loadItems();

        $.ajax({
            url: `${ API }/notifications/read-all`,
            method: 'put',
            success: function (respond) {
                this.setState({
                    total_count: 0
                });
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    }

});

const Item = React.createClass({
    render () {
        const item = this.props;
        let link = `/posts/${ item.post.id }`;
        link = link + (window.location.pathname.search(link) > -1 ? '#reload' : '');
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

const List = React.createClass({

    getInitialState () {
        return {
            loading: false,
            items: []
        };
    },


    componentWillMount () {
        LIST.component = this;
        this.loadItems();
    },

    loadItems(){
        let url = PROD ? `${ API }/notifications` : '/data/notification.json';

        this.setState({
            items: [],
            loading: false
        }, function () {

            $.getJSON(url, {
                expand: 'actors',
                'per-page': 5
            }, function (result, t, requst) {

                let count = requst.getResponseHeader('X-Pagination-Total-Count');

                if (this.isMounted()) {

                    this.setState({
                        items: result,
                        loading: true
                    });
                }

                //NotifyComponent.update({
                //    total_count: count > 99 ? 99 : count
                //});

            }.bind(this));
        });
    },

    render () {

        return (
            <div
                id="drop_notification_menu"
                data-dropdown-content
                className="f-dropdown content notification_list_"
                aria-hidden="true"
                tabIndex="-1">

                <h4 className="title_">Atividades</h4>

                <ul className="no-bullet">
                    {
                        (!this.state.loading /*&& this.state.items <= 0*/) && (
                            <li className="unstyled_">Carregando...</li>)
                    }
                    {
                        (this.state.loading && this.state.items.length <= 0) && (
                            <li className="unstyled_">Sem atividade</li>)
                    }
                    {
                        this.state.loading &&
                        this.state.items.map(function item(data, i) {
                            return (
                                <Item key={i} { ...data }/>
                            );
                        })
                    }
                </ul>
                <footer className="text-center">
                    <Link to="/notifications">Ver todas</Link>
                </footer>
            </div>
        );
    }

});

export default {
    Notification: Notification,
    NotificationItem: Item,
    NotificationList: List
};
