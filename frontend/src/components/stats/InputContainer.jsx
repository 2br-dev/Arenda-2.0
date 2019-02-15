import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import MenuItem from '@material-ui/core/MenuItem';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';
import styled from 'styled-components';
import selectData from './selectData';
import $ from 'jquery';
import green from '@material-ui/core/colors/green';
import ErrorIcon from '@material-ui/icons/Error';
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import InfoIcon from '@material-ui/icons/Info';
import CloseIcon from '@material-ui/icons/Close';
import WarningIcon from '@material-ui/icons/Warning';
import IconButton from '@material-ui/core/IconButton';
import classNames from 'classnames';
import Snackbar from '@material-ui/core/Snackbar';
import SnackbarContent from '@material-ui/core/SnackbarContent';
import Card from '@material-ui/core/Card';
import CardContent from '@material-ui/core/CardContent';
import CardMedia from '@material-ui/core/CardMedia';
import Paper from '@material-ui/core/Paper';
import Image from '../../images/wall.jpg';

const variantIcon = {
  success: CheckCircleIcon,
  warning: WarningIcon,
  error: ErrorIcon,
  info: InfoIcon,
};

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

const MySnackbarContentWrapper = withStyles(styles1)(MySnackbarContent);

class InputContainer extends Component {
  state = {
    year: '',
    month: '',
    type: 'Электроэнергия',
    amount: '',
    summa: '',
    summaError: false,
    amountError: false,
    open: false
  };

  // устанавливаем сегодняшний месяц и год
  componentDidMount = () => {
    const date = new Date();
    this.setState({ year: date.getFullYear(), month: date.getMonth() + 1})
  }
  
  handleChange = name => event => {
    this.setState({ [name]: event.target.value, summaError: false, amountError: false });
  };

  // динамически создаем селект
  createSelect = (id, name, array) => {
    const { classes } = this.props;

    return (
      <TextField
        id={id}
        name={id}
        select
        label={`Выберите ${name}`}
        className={classes.textField}
        value={this.state[id]}
        onChange={this.handleChange(id)}
        SelectProps={{
          MenuProps: {
            className: classes.menu,
          },
        }}
        margin="normal"
      >
        {array.map(option => (
          <MenuItem key={option.value} value={option.value}>
            {option.label}
          </MenuItem>
        ))}
      </TextField>
    )
  }

  onSubmit = e => {
    e.preventDefault();
    const { year, month, type, amount, summa } = this.state;
    const self = this;
    if (amount && summa) {
      $.ajax({
        type: "POST",
        url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/communal/write.php`,
        data: { year, month, type, amount, summa },
        success: function (res) {
          console.log(res);
          if (res.result === 1) {
            self.setState({ open: true, amount: '', summa: '' })
            $('#comunal').trigger('reset');
          };
        },
        error: function (err) {
          console.log(err);
        }
      });
    } else {
      if (!summa) this.setState({ summaError: true })
      if (!amount) this.setState({ amountError: true })
    } 
  }

  handleClose = (event, reason) => {
    if (reason === 'clickaway') {
      return;
    }
    this.setState({ open: false, success: false, warning: false });
  };

  render() {
    const { classes } = this.props;

    return (
      <Paper className={classes.wrapper}>
      <Card className={classes.card}>
        <CardContent className={classes.content}>
        <Form id='comunal' className={classes.container} noValidate autoComplete="off" onSubmit={this.onSubmit}>
        {this.createSelect('year', 'год', selectData.years)}
        {this.createSelect('month', 'месяц', selectData.months)}
        {this.createSelect('type', 'тип', selectData.types)}
        
        <Wrapper><TextField
          id="summa"
          label="Сумма"
          error={this.state.summaError}
          value={this.state.summa}
          onChange={this.handleChange('summa')}
          helperText={this.state.summaError ? `Пожалуйста введите сумму` : ''}
          type="number"
          required
          className={classes.textField}
          InputLabelProps={{
            shrink: true,
          }}
          margin="normal"
        /></Wrapper>

        <Wrapper><TextField
          id="amount"
          label="Количество"
          required
          error={this.state.amountError}
          value={this.state.amount}
          onChange={this.handleChange('amount')}
          helperText={this.state.amountError ? `Пожалуйста введите количество` : ''}
          type="number"
          className={classes.textField}
          InputLabelProps={{
            shrink: true,
          }}
          margin="normal"
        /></Wrapper>

        <Button type="submit">Отправить</Button>
      </Form>

      {this.state.open ? (
        <Snackbar
          anchorOrigin={{
            vertical: 'bottom',
            horizontal: 'left',
          }}
          open={this.state.open}
          autoHideDuration={5000}
          onClose={this.handleClose}
        >
          <MySnackbarContentWrapper
            onClose={this.handleClose}
            variant="success"
            className={classes.margin}
            message="Успешно! Данные занесены в Базу Данных"
          />
        </Snackbar>
      ) : null}
        </CardContent>
        <CardMedia
          className={classes.cover}
          image={Image}
        ></CardMedia>
      </Card>
      </Paper>
    );
  }
}

InputContainer.propTypes = {
  classes: PropTypes.object.isRequired,
};

const Wrapper = styled.div`
  input {
    border: none !important;
    width: 350px !important;
  }
`
const Form = styled.form`
  display: flex;
  flex-direction: column;
  align-items: center;

  button {
    background: #232323;
    color: #fff;
    margin-top: 10px;
    &:hover {
      background: black;
    }
  }
`

const styles = theme => ({
  container: {
    display: 'flex',
    flexWrap: 'wrap',
  },
  wrapper: {
    padding: 60
  },
  textField: {
    marginLeft: theme.spacing.unit,
    marginRight: theme.spacing.unit,
    width: 350,
  },
  menu: {
    width: 350,
  },
  card: {
    width: 900,
    height: '90%',
    margin: 'auto',
    display: 'flex',
  },
  content: {
    flex: '1 0 auto',
  },
  cover: {
    width: 480,
  },
});

export default withStyles(styles)(InputContainer);