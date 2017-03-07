import { FacebookButton, TwitterButton } from "../../services/socialShare";

export default React.createClass({
    render () {
        return (
            <ul className="share_ button-group right">

                <li>
                    <FacebookButton
                        className="button facebook_"
                        id={ this.props.id }
                        description={ this.props.description }
                        photo={ this.props._links.photo.href }
                    >
                        <i className="icon-facebook"></i>
                    </FacebookButton>
                </li>
                <li>
                    <TwitterButton
                        className="button twitter_"
                        id={ this.props.id }
                        text={ this.props.description }
                    >
                        <i className="icon-twitter"></i>
                    </TwitterButton>
                </li>

            </ul>
        )
    }
});


