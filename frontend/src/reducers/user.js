export default function arenda(state = [], action) {
  switch (action.type) {
    case 'LOGGED_IN':
      return [action.payload]
    default:
      return state
  }
}