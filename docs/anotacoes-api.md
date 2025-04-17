# Tabelafipe (GET)

- carros/marcas
- motos/marcas
- carros/marcas/{x}/modelos/
- motos/marcas/{x}/modelos/
- carros/marcas/{x}/modelos/{y}/anos/
- motos/marcas/{x}/modelos/{y}/anos/
- carros/marcas/{x}/modelos/{y}/anos/{z}
- motos/marcas/{x}/modelos/{y}/anos/{z} ->
  {
    TipoVeiculo: 1
    Valor: "R$ 96.382,00"
    Marca: "VW - VolksWagen"
    Modelo: "AMAROK High.CD 2.0 16V TDI 4x4 Dies. Aut"
    AnoModelo: 2014
    Combustivel: "Diesel"
    CodigoFipe: "005340-6"
    MesReferencia: "abril de 2025"
    SiglaCombustivel: "D"
  }

## Senha PEM

- 1234

## Entidades

- **User** possui os campos(por enquanto)
  - email
  - senha
  - id
  - role

- **Category**
  - id
  - nome

- **Brand**
  - id
  - fipeCode
  - nome

- **Model**
  - id
  - fipeCode
  - nome
