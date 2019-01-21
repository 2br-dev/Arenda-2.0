import React from 'react';
import './css/Modal.css';
import './css/animate.css';

const Modal = props => {
  return (
    <React.Fragment>
    {props.visible ? (
      <React.Fragment>
      <div id="modal" className="modal bounceInDown animated">
        <button className="close" onClick={props.toggleModal}></button>
        <p>{props.text}</p>
      </div>
      <div className="black-wrapper" onClick={props.toggleModal}></div>
      </React.Fragment> ) 
      : null}
    </React.Fragment>  
  )
}

export default Modal;