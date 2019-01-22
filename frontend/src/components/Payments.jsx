import React, { Component } from 'react'
import { connect } from 'react-redux'
import styled from 'styled-components'
import Loader from './Loader'
import Modal from './Modal'
import $ from 'jquery';

class Payments extends Component {
  constructor(props){
    super(props);
    this.state = {
      loading: false,
      modal: false,
      modalText: '',
      date: this.setToday(),
      summa: 0,
      number: 0,
      selectedRenter: '',
      selectedContract: ''
    }
    this.setToday = this.setToday.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  componentDidMount = () => {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/renters/read.php`)
      .then(response => response.json())
      .then(res => this.props.getRenters(res)) 
      .then(() => this.setState({ loading: false }))
      .catch(err => console.log(err)) 
  }

  // оплата
  handleSubmit = e => {
    e.preventDefault();
    const self = this;
    const { summa, date, number, selectedRenter, selectedContract } = this.state;
    const store = this.props.testStore;

    // добавляем в массив все счета
    let invoices = [];
    $("input[name='invoice_number']:checked").each(function() {
      invoices.push($(this).val());
    });
    invoices.sort(this.sortNumber); // сортируем

    const data = {
      summa:              parseFloat(summa.replace(/,/g,'.')).toFixed(2), // требуемый формат
      date:               date,
      number:             number,
      renter_id:          selectedRenter,
      renter_name:        store.renters.find(renter => renter.id === selectedRenter).full_name, // находим имя арендатора
      renter_document:    store.contracts.find(contract => contract.id === selectedContract).number, // находим номер договора
      contract_id:        selectedContract,
      invoices:           invoices,
    };
    
  console.log(data);
 
  // если все поля есть - то делаем запрос
  if (data.summa && data.date && data.number && data.renter_name && data.renter_document && data.contract_id && data.invoices.length !== 0) {
      $.ajax({
        type: "POST",
        data: data,
        url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/actions/payment.php`,
        success: function(res){
          console.log(res);
          self.openModal('Успешно!');
          $('#payments').trigger("reset"); // при успехе ресет формы
        },
        error: function(err) {
          console.log(err);
        }
      });
    } else {
      self.openModal('Заполните все поля');
    } 
  }

  // ф-я сортировки
  sortNumber = (a, b) => a - b;

  // выбираем арендатора и делаем запрос на получение договоров
  selectRenter = e => {
    if (this.state.selectedRenter !== '') {
      this.setState({ selectedContract: '' });
      this.props.clearContracts();
      this.props.clearInvoices();
    } 
    e.preventDefault();
    const self = this;
    const target = e.target;
    $.ajax({
      type: "POST",
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/contract/read_one.php`,
      data: { id: target.value },
      success: function(res){
        self.setState({ selectedRenter: target.value });
        self.props.getContracts(res);
      },
      error: function(err) {
        console.log(err);
      }
    });
  }

  // выбираем договор и делаем запрос на получение счетов
  selectContract = e => {
    e.preventDefault();
    const self = this;
    const target = e.target;
    $.ajax({
      type: "POST",
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/invoice/read_by_contract.php`,
      data: { id: target.value },
      success: function(res){
        self.setState({ selectedContract: target.value });
        self.props.getInvoices(res);
        if(res.length === 0) {
          self.openModal('Нет счетов по выбранному договору');
        }
      },
      error: function(err) {
        console.log(err);
      }
    });
  }

  // устанавливаем сегодняшнюю дату
  setToday = () => {
    let date = new Date(),
        day = date.getDate(),
        month = date.getMonth() + 1,
        year = date.getFullYear();
    if (day < 10) day = '0' + day;
    if (month < 10)  month = '0' + month;
    return year + '-' + month + '-' + day;
  }

  // ф-я показывающая модальное окно
  openModal = (text) => {
    this.setState({ modal: true, modalText: text});
  }
  // передаём в Modal для переключения видимости
  toggleModal = () => this.setState({ modal: false, modalText: ''});

  render() {
    const { date, loading, selectedRenter, selectedContract } = this.state;
    const store = this.props.testStore;

    return (
      <section className='container' style={{'width':'600px'}}>
        {loading ? <Loader/> : (
        <Form id='payments' method='post' action='' onSubmit={this.handleSubmit}>
  
        <Select name='selected-renter' onChange={this.selectRenter}>
          <option selected disabled hidden value={selectedRenter}>
            {selectedRenter === '' ? 'Выберите арендатора' : store.renters.find(renter => renter.id === selectedRenter).short_name }
          </option>
          {store.renters.map((renter, i) => {  
            return (
              <option key={i} value={renter.id}>{renter.short_name}</option>
            )
          })}
        </Select>
        
        {selectedRenter !== '' 
        ? 
        <Select name='selected-contract' onChange={this.selectContract} style={{ 'top':'90px' }}>
          <option selected hidden={selectedContract} value={selectedContract}>
            {selectedContract === '' ? 'Выберите договор' : store.contracts.find(contract => contract.id === selectedContract).number }
          </option>
          {store.contracts.map((contract, i) => {  
            return (
              <option key={i} name='selected-contract' value={contract.id}>{contract.number} от {contract.datetime}</option>
            )
          })}
        </Select>
        :null}

        {store.invoices.map((invoice, i) => {  
          return ( 
          <React.Fragment key={i}>
          <hr /> 
          <Label    
            className='container-checkbox' 
            style={{'marginBottom':'0'}}
          >
          Номер счёта {invoice.invoice_number} от <b>{invoice.invoice_date}</b>, остаток по счёту: <b> {invoice.rest}</b>
            <input name='invoice_number' type='checkbox' value={invoice.invoice_number} />
            <span className='checkmark'></span>
          </Label>
          </React.Fragment>
          )
        })}       
    
        {store.invoices.length !== 0 && selectedRenter !== '' && selectedContract !== '' 
        ?
        <React.Fragment>
          <p style={{'marginTop':'50px'}}><b>Данные платежа</b></p>

          <FormRow>
            <label htmlFor='sum'>Сумма платежа:</label>
            <input type='text' id='sum' name='summa' onChange={e => this.setState({summa: e.target.value})} />
          </FormRow>  
          <FormRow>
            <label htmlFor='date'>Дата платежа:</label>
            <input type='date' id='date' name='date' value={date} onChange={e => this.setState({date: e.target.value})} />
          </FormRow>  
          <FormRow>
            <label htmlFor='number'>Номер платежа:</label>
            <input type='text' id='number' name='document_number' onChange={e => this.setState({number: e.target.value})} />
          </FormRow>  

          <hr style={{'marginTop':'40px'}} />

          <button type='submit' className='btn'>Отправить</button> 
        </React.Fragment>
        : null}

        </Form> )}

      {this.state.modal ? <Modal text={this.state.modalText} visible={this.state.modal} toggleModal={this.toggleModal} /> : null}

    </section>    
    )
  }
}

const Select = styled.select`
  color: #fff;
  background: #232323;
  border: 0;
  padding: 10px 20px;
  border-radius: 10px;
  margin-bottom: 15px;
  width: 300px;
  position: absolute;
  left: 0;
  right: 0;
  margin: auto;
  top: 50px;
`;

const Form = styled.form`
  margin-top: 125px;
  text-align: center;
  b {
    font-size: 1.13em;
  }
  .btn {
    margin-top: 30px;
  }
`;

const FormRow = styled.div`
  display: flex;
  align-items: center;
  justify-content: space-between;
  input {
    width: 80%;
    background: #fff;
  }
  margin-bottom: 10px;
`;

const Label = styled.label`
  display: flex;
  align-items: center;
  b {
    font-size: 1em;
    margin-left: 5px;
  }
  margin-bottom: 10px;
`;

export default connect(
  state => ({
    testStore: state
  }),
  dispatch => ({
    getRenters: (renters) => {
      dispatch({ type: 'FETCH_ALL_RENTERS', payload: renters})
    },
    getContracts: (contract) => {
      dispatch({ type: 'FETCH_SINGLE_CONTRACT', payload: contract})
    },
    getInvoices: (invoices) => {
      dispatch({ type: 'FETCH_INVOICES_BY_ID', payload: invoices})
    },
    clearContracts: () => dispatch({ type: 'CLEAR_CONTRACTS'}),
    clearInvoices: () => dispatch({ type: 'CLEAR_INVOICES'})
  })
)(Payments);