import React, { Component } from 'react';
import './css/Modal.css';
import './css/animate.css';

export default class Modal extends Component {
  render() {
    return ( 
      <>
      {this.props.visible ? ( 
        <>
        <div id="modal" className="modal bounceInDown animated">
          <button className="close" onClick={this.props.toggleModal}></button>
          <p>{this.props.text}</p>
        </div>
        <div className="black-wrapper" onClick={this.props.toggleModal}></div></>) 
        : null
      }
      </>
    )
  } 
}