export const cityProvide = Symbol('city')
export const API_ENDPOINT = 'http://api.weatherapi.com/v1'

export const errorMap = new Map(
  [
    [
      400, 'Указанный город не найден'
    ]
  ]
)
