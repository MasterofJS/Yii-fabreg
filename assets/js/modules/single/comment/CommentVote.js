//import React from 'react';
export default React.createClass({

    getInitialState(){
        return {
            points: this.props.points,
            classUp: (1 === this.props.liked),
            classDown: (-1 === this.props.liked)
        }
    },

    render () {

        let classesUp = classNames({
            'active': this.state.classUp
        });
        let classesDown = classNames({
            'active': this.state.classDown
        });

        return (
            <ul className="comment_vote_ no-bullet">
                <li>
                    <a href="#up" className={classesUp} onClick={this.onClickUp}>
                        <i className="icon-thumbs-up"></i>
                    </a>
                </li>

                <li>
                    <a href="#down" className={classesDown} onClick={this.onClickDown}>
                        <i className="icon-thumbs-down"></i>
                    </a>
                </li>
            </ul>

        );
    },

    onClickUp(e){
        let points = this.state.points;
        e.preventDefault();

        if (this.state.classUp) {
            this.setState({
                classUp: false,
                points: points - 1
            });
            this.props.handleLike(points - 1);
            return;
        }

        points += (this.state.classDown ? 2 : 1);
        this.setState({
            classDown: false,
            classUp: true,
            points: points
        });

        this.props.handleLike(points);
    },

    onClickDown(e){
        let points = this.state.points;
        e.preventDefault();

        if (this.state.classDown) {
            this.setState({
                classDown: false,
                points: points + 1
            });
            this.props.handleLike(points + 1);
            return;
        }

        points -= (this.state.classUp ? 2 : 1);
        this.setState({
            classUp: false,
            classDown: true,
            points: points
        });

        this.props.handleLike(points);
    }
});