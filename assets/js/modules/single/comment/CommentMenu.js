import { API } from '../../../modules/config';

export default React.createClass({

    getInitialState () {
        return {
            mute: this.props.author.can_viewer_mute
        };
    },

    render () {
        if (!(this.props.can_viewer_report || this.state.mute || this.props.can_viewer_delete || this.props.author.can_viewer_unmute)) {
            return null;
        }
/*todo*/
        return (
            <div className="menu_">

                <a href="#" data-dropdown={`drop_${this.props.id}`} className="dropdown">
                    <i className="icon-down-open"></i>
                </a>

                <ul id={`drop_${this.props.id}`} data-dropdown-content className="f-dropdown" aria-hidden="true">
                    {
                        this.props.can_viewer_report &&
                        <li>
                            <a href="#" onClick={this._report}>Esconder comentário</a>
                        </li>
                    }
                    {
                        this.props.can_viewer_delete &&
                        <li>
                            <a href="#" onClick={this._delete}>Deletar</a>
                        </li>
                    }
                    {
                        this.state.mute === true &&
                        <li>
                            <a href="#" onClick={this._mute}>Calar @{this.props.author.username}</a>
                        </li>
                    }
                    {
                        (this.state.mute === false || this.props.author.can_viewer_unmute === true) &&
                        <li>
                            <a href="#" onClick={this._unmute}>Não calar @{this.props.author.username}</a>
                        </li>
                    }
                </ul>

            </div>
        );
    },

    _report(e) {
        e.preventDefault();

        $.ajax({
            url: `${ API }/comments/${ this.props.id }/report`,
            method: 'post',
            success: function () {
                var newData;
                const that = this;
                const key = !this.props.rootThat.state.answers ? 'data' : 'answers';

                newData = this.props.rootThat.state[key].filter(function (e) {
                    return e.id !== that.props.id;
                });

                this.props.rootThat.setState({
                    [key]: newData
                });
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    },

    _delete(e){
        e.preventDefault();

        $.ajax({
            url: `${ API }/comments/${ this.props.id }`,
            method: 'delete',
            success: function () {
                var newData;
                const that = this;
                const key = !this.props.rootThat.state.answers ? 'data' : 'answers';

                newData = this.props.rootThat.state[key].filter(function (e) {
                    return e.id !== that.props.id;
                });

                this.props.rootThat.setState({
                    [key]: newData
                });

            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        })
        ;
    },

    _mute(e){
        e.preventDefault();

        $.ajax({
            url: `${ API }/users/${ this.props.author.username }/mute`,
            method: 'post',
            success: function () {
                this.setState({
                    mute: false
                });
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    },

    _unmute(e){
        e.preventDefault();

        $.ajax({
            url: `${ API }/users/${ this.props.author.username }/unmute`,
            method: 'delete',
            success: function () {
                this.setState({
                    mute: true
                });
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    }
});