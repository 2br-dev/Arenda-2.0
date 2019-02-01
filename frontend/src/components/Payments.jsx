import React, { Component } from 'react'
import { connect } from 'react-redux'
import styled from 'styled-components'
import Loader from './Loader'
import $ from 'jquery'
import Typography from '@material-ui/core/Typography'
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import ErrorIcon from '@material-ui/icons/Error';
import InfoIcon from '@material-ui/icons/Info';
import CloseIcon from '@material-ui/icons/Close';
import green from '@material-ui/core/colors/green';
import amber from '@material-ui/core/colors/amber';
import IconButton from '@material-ui/core/IconButton';
import classNames from 'classnames';
import PropTypes from 'prop-types';
import Snackbar from '@material-ui/core/Snackbar';
import SnackbarContent from '@material-ui/core/SnackbarContent';
import WarningIcon from '@material-ui/icons/Warning';
import { withStyles } from '@material-ui/core/styles';
import Modal from './Modal';

const variantIcon = {
  success: CheckCircleIcon,
  warning: WarningIcon,
  error: ErrorIcon,
  info: InfoIcon,
};

const styles1 = theme => ({
  success: {
    backgroundColor: green[600],
  },
  error: {
    backgroundColor: theme.palette.error.dark,
  },
  info: {
    backgroundColor: theme.palette.primary.dark,
  },
  warning: {
    backgroundColor: amber[700],
  },
  icon: {
    fontSize: 20,
  },
  iconVariant: {
    opacity: 0.9,
    marginRight: theme.spacing.unit,
  },
  message: {
    display: 'flex',
    alignItems: 'center',
  },
});

function MySnackbarContent(props) {
  const { classes, className, message, onClose, variant, ...other } = props;
  const Icon = variantIcon[variant];

  return (
    <SnackbarContent
      className={classNames(classes[variant], className)}
      aria-describedby="client-snackbar"
      message={
        <span id="client-snackbar" className={classes.message}>
          <Icon className={classNames(classes.icon, classes.iconVariant)} />
          {message}
        </span>
      }
      action={[
        <IconButton
          key="close"
          aria-label="Close"
          color="inherit"
          className={classes.close}
          onClick={onClose}
        >
          <CloseIcon className={classes.icon} />
        </IconButton>,
      ]}
      {...other}
    />
  );
}

MySnackbarContent.propTypes = {
  classes: PropTypes.object.isRequired,
  className: PropTypes.string,
  message: PropTypes.node,
  onClose: PropTypes.func,
  variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired,
};

const MySnackbarContentWrapper = withStyles(styles1)(MySnackbarContent);

const styles2 = theme => ({
  margin: {
    margin: theme.spacing.unit,
    position: 'fixed',
    left: 20,
    margin: 'auto',
    width: 'fit-content',
    bottom: 20,
    zIndex: 50,
    maxWidth: 600
  },
});

class Payments extends Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: false,
      modal: false,
      date: this.setToday(),
      summa: 0,
      number: 0,
      selectedRenter: '',
      selectedContract: '',
      modalText: '',
      open: false
    }
    this.setToday = this.setToday.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  componentDidMount = () => {
    this.fetchData();
  }

  fetchData = () => {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/renters/read.php`)
      .then(response => response.json())
      .then(res => this.props.getRenters(res))
      .then(() => this.setState({ loading: false }))
      .catch(err => console.log(err))
  }

  fetchContracts = () => {
    const self = this;
    $.ajax({
      type: "POST",
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/contract/read_one.php`,
      data: { id: self.state.selectedRenter },
      success: function (res) {
        self.props.getContracts(res);
      }
    });
  }

  // оплата
  handleSubmit = e => {
    e.preventDefault();
    const self = this;
    const { summa, date, number, selectedRenter, selectedContract } = this.state;
    const store = this.props.testStore;

    // если все поля есть - то делаем запрос
    if (summa && date && number && selectedRenter && selectedContract) {
      const data = {
        summa: parseFloat(summa.replace(/,/g, '.')).toFixed(2), // требуемый формат
        date: date,
        number: number,
        renter_id: selectedRenter,
        renter_name: store.renters.find(renter => renter.id === selectedRenter).full_name, // находим имя арендатора
        renter_document: store.contracts.find(contract => contract.id === selectedContract).number, // находим номер договора
        contract_id: selectedContract
      };

      console.log(data);

      $.ajax({
        type: "POST",
        data: data,
        url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/actions/payment.php`,
        success: function (res) {
          console.log(res);

          if (res.result === 1) {
            self.setState({
              warning: false,
              open: true,
              success: true,
              summa: 0,
              number: 0
            });
            self.fetchData();
            self.fetchContracts();
            $('#payments').trigger("reset"); // при успехе ресет формы 
          }
          if (res.payed) {
            self.setState({
              success: false,
              open: true,
              warning: true
            });
          }

        },
        error: function (err) {
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
      success: function (res) {
        self.setState({ selectedRenter: target.value });
        self.props.getContracts(res);
      },
      error: function (err) {
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
      success: function (res) {
        self.setState({ selectedContract: target.value });
        self.props.getInvoices(res);
        if (res.length === 0) {
          self.openModal('Нет счетов по выбранному договору');
        }
      },
      error: function (err) {
        console.log(err);
      }
    });
  }

  handleClose = (event, reason) => {
    if (reason === 'clickaway') {
      return;
    }
    this.setState({ open: false, success: false, warning: false });
  };

  // устанавливаем сегодняшнюю дату
  setToday = () => {
    let date = new Date(),
      day = date.getDate(),
      month = date.getMonth() + 1,
      year = date.getFullYear();
    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;
    return year + '-' + month + '-' + day;
  }

  // ф-я показывающая модальное окно
  openModal(text) {
    this.setState({ modal: true, modalText: text });
  }
  // передаём в Modal для переключения видимости
  toggleModal = () => this.setState({ modal: false, modalText: '' });


  render() {
    const { date, loading, selectedRenter, selectedContract, warning, success, modal, open } = this.state;
    const { classes } = this.props;
    const store = this.props.testStore;

    return (
      <section className='container' style={{ 'width': '600px' }}>

        {warning && open ? (
          <Snackbar
            anchorOrigin={{
              vertical: 'bottom',
              horizontal: 'left',
            }}
            open={open}
            autoHideDuration={5000}
            onClose={this.handleClose}
          >
            <MySnackbarContentWrapper
              onClose={this.handleClose}
              variant="error"
              className={classes.margin}
              message="Такая оплата уже существует. Проверьте правильность введённых данных."
            />
          </Snackbar>
        ) : null}

        {success && open ? (
          <Snackbar
            anchorOrigin={{
              vertical: 'bottom',
              horizontal: 'left',
            }}
            open={open}
            autoHideDuration={5000}
            onClose={this.handleClose}
          >
            <MySnackbarContentWrapper
              onClose={this.handleClose}
              variant="success"
              className={classes.margin}
              message="Оплата успешно занесена в базу данных."
            />
          </Snackbar>
        ) : null}

        {loading ? <Loader /> : (
          <Form id='payments' method='post' action='' onSubmit={this.handleSubmit}>

            <Select name='selected-renter' onChange={this.selectRenter}>
              <option selected disabled hidden value={selectedRenter}>
                {selectedRenter === '' ? 'Выберите арендатора' : store.renters.find(renter => renter.id === selectedRenter).short_name}
              </option>
              {store.renters.map((renter, i) => {
                return (
                  <option key={i} value={renter.id}>{renter.short_name}</option>
                )
              })}
            </Select>

            {selectedRenter !== ''
              ?
              <Select name='selected-contract' onChange={this.selectContract} style={{ 'top': '90px' }}>
                <option selected hidden={selectedContract} value={selectedContract}>
                  {selectedContract === '' ? 'Выберите договор' : store.contracts.find(contract => contract.id === selectedContract).number}
                </option>
                {store.contracts.map((contract, i) => {
                  return (
                    <option key={i} name='selected-contract' value={contract.id}>{contract.number} от {contract.datetime}</option>
                  )
                })}
              </Select>
              : null}

            {selectedRenter !== '' && selectedContract !== '' && store.invoices.length != 0
              ?
              <React.Fragment>

                <Typography variant="subtitle2" style={{ textAlign: 'left' }}>Общий баланс по договору:
            <span className='ml5'
                    style={store.contracts.find(contract => contract.id == selectedContract).balance < 0 ? { color: 'red' } : { color: 'green' }}
                  >
                    {parseFloat(store.contracts.find(contract => contract.id == selectedContract).balance).toFixed(2)} ₽
            </span>
                </Typography>
                <Typography variant="subtitle2" style={{ textAlign: 'left' }}>Общий баланс арендатора:
            <span className='ml5'
                    style={store.renters.find(renter => renter.id == selectedRenter).balance < 0 ? { color: 'red' } : { color: 'green' }}
                  >
                    {parseFloat(store.renters.find(renter => renter.id == selectedRenter).balance).toFixed(2)} ₽
            </span>
                </Typography>

                <p><b>Данные платежа</b></p>

                <FormRow>
                  <label htmlFor='sum'>Сумма платежа:</label>
                  <input type='text' id='sum' name='summa' onChange={e => this.setState({ summa: e.target.value })} />
                </FormRow>
                <FormRow>
                  <label htmlFor='date'>Дата платежа:</label>
                  <input type='date' id='date' name='date' value={date} onChange={e => this.setState({ date: e.target.value })} />
                </FormRow>
                <FormRow>
                  <label htmlFor='number'>Номер платежа:</label>
                  <input type='text' id='number' name='document_number' onChange={e => this.setState({ number: e.target.value })} />
                </FormRow>

                <hr style={{ 'marginTop': '40px' }} />

                <button type='submit' className='btn'>Отправить</button>
              </React.Fragment>
              : null}

          </Form>)}

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
  label {
    text-align: left;
  }
  input {
    width: 70%;
    background: #fff;
  }
  margin-bottom: 10px;
`;



export default connect(
  state => ({
    testStore: state
  }),
  dispatch => ({
    getRenters: (renters) => {
      dispatch({ type: 'FETCH_ALL_RENTERS', payload: renters })
    },
    getContracts: (contract) => {
      dispatch({ type: 'FETCH_SINGLE_CONTRACT', payload: contract })
    },
    getInvoices: (invoices) => {
      dispatch({ type: 'FETCH_INVOICES_BY_ID', payload: invoices })
    },
    clearContracts: () => dispatch({ type: 'CLEAR_CONTRACTS' }),
    clearInvoices: () => dispatch({ type: 'CLEAR_INVOICES' })
  })
)(withStyles(styles2)(Payments));