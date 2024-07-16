var spec = {
    "openapi": "3.0.1",
    "info": {
      "version": "1.0.0",
      "title": "Quickbee API",
      "description": "Pour se connecter à l'API, vous devez renseigner votre clé API (API-KEY) dans l'en-tête (header) de chaque requête."
    },
    "servers": [{
        "url": window.PATH
    }],
    "paths": {
      "/product": {
        "get": {
            "summary": "Get a products list",
            "operationId": "Products",
            "tags": [
                "Product"
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetProductList"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
      "/product/{id}": {
        "get": {
            "summary": "Get a product",
            "operationId": "Product",
            "tags": [
                "Product"
            ],
            "parameters": [
                {
                  "$ref": "#/components/parameters/Id"
                }
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetProduct"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
      "/client": {
        "get": {
            "summary": "Get a clients list",
            "operationId": "ClientList",
            "tags": [
                "Client"
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetClientList"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
      "/client/{id}": {
        "get": {
            "summary": "Get a client",
            "operationId": "Client",
            "tags": [
                "Client"
            ],
            "parameters": [
                {
                  "$ref": "#/components/parameters/Id"
                }
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetClient"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
      "/invoice": {
        "get": {
            "summary": "Get a invoices list",
            "operationId": "InvoiceList",
            "tags": [
                "Invoice"
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetInvoiceList"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
      "/invoice/{id}": {
        "get": {
            "summary": "Get a invoice",
            "operationId": "Invoice",
            "tags": [
                "Invoice"
            ],
            "parameters": [
                {
                  "$ref": "#/components/parameters/Id"
                }
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetInvoice"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
      "/quotation": {
        "get": {
            "summary": "Get a quotations list",
            "operationId": "QuotationList",
            "tags": [
                "Quotation"
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetQuotationList"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        },
      },
        "/quotation/{id}": {
        "get": {
            "summary": "Get a quotation",
            "operationId": "Quotation",
            "tags": [
                "Quotation"
            ],
            "parameters": [
                {
                  "$ref": "#/components/parameters/Id"
                }
            ],
            "responses": {
            "200": {
              "description": "Success",
              "content": {
                "application/json": {
                  "schema": {
                    "$ref": "#/components/schemas/GetQuotation"
                  }
                }
              }
            },
            "404": {
              "$ref": "#/components/responses/NotFound"
            }
          }
        }
      },
    },
    "components": {
      "schemas": {
        "GetProductList": {
          "type": "array",
          "items": {
            "$ref": "#/components/schemas/GetProduct"
          }
        },
        "GetProduct": {
          "properties": {
            "id": {
              "description": "Resource ID",
              "type": "int",
              "maxLength": 1024,
              "example": "1"
            },
            "name": {
              "description": "Name of product",
              "type": "string",
              "maxLength": 1024,
              "example": "Shoes"
            },
            "description": {
                "description": "Description of product",
                "type": "string",
                "maxLength": 1024,
                "example": "Running shoes"
              },
            "tva": {
              "description": "TVA of product",
              "type": "double",
              "maxLength": 1024,
              "example": "0.2"
            },
            "price_ht": {
                "description": "Price HT of product",
                "type": "double",
                "maxLength": 1024,
                "example": "169.99"
              },
          }
        },
        "GetClientList": {
          "type": "array",
          "items": {
            "$ref": "#/components/schemas/GetClient"
          }
        },
        "GetClient": {
          "properties": {
            "id": {
              "description": "Resource ID",
              "type": "int",
              "maxLength": 1024,
              "example": "1"
            },
            "compagny_name": {
              "description": "Company of client",
              "type": "string",
              "maxLength": 1024,
              "example": "ESGI"
            },
            "first_name": {
                "description": "First name of client",
                "type": "string",
                "maxLength": 1024,
                "example": "John"
              },
            "last_name": {
              "description": "Last name of client",
              "type": "string",
              "maxLength": 1024,
              "example": "Doe"
            },
            "email": {
                "description": "Email of client",
                "type": "string",
                "maxLength": 1024,
                "example": "john.doe@esgi.fr"
            },
            "phone": {
                "description": "Phone number of client",
                "type": "string",
                "maxLength": 1024,
                "example": "0732984589"
            },
            "siren": {
                "description": "SIREN number of client",
                "type": "string",
                "maxLength": 1024,
                "example": "362521879"
            },
            "iban": {
                "description": "IBAN number of client",
                "type": "string",
                "maxLength": 1024,
                "example": "FR1420041010050500013M02606"
            },
            "address": {
                "description": "Adress of client",
                "type": "string",
                "maxLength": 1024,
                "example": "242 Rue du Faubourg Saint-Antoine"
            },
            "postal_code": {
                "description": "Postal code of client",
                "type": "string",
                "maxLength": 1024,
                "example": "75012"
            },
            "city": {
                "description": "City of client",
                "type": "string",
                "maxLength": 1024,
                "example": "Paris"
            },
            "country": {
                "description": "Country of client",
                "type": "string",
                "maxLength": 1024,
                "example": "France"
            },
          }
        },
        "GetInvoiceList": {
          "type": "array",
          "items": {
            "$ref": "#/components/schemas/GetInvoice"
          }
        },
        "GetInvoice": {
            "properties": {
              "id": {
                "description": "Resource ID",
                "type": "int",
                "maxLength": 1024,
                "example": "1"
              },
              "name": {
                "description": "Name of invoice",
                "type": "string",
                "maxLength": 1024,
                "example": "196b7e02-88a6-4b40-8ae1-002f7e59fca0"
              },
              "client_id": {
                "description": "Client ID of invoice",
                "type": "string",
                "maxLength": 1024,
                "example": "1"
              },
              "client": {
                "description": "First name and last name of client",
                "type": "string",
                "maxLength": 1024,
                "example": "John Doe"
              },
              "issue_date": {
                "description": "Issue date of invoice",
                "type": "string",
                "maxLength": 1024,
                "example": "09/06/2024"
              },
              "due_date": {
                "description": "Due date of invoice",
                "type": "string",
                "maxLength": 1024,
                "example": "19/06/2024"
              },
              "infos": {
                "description": "Complémentary informations",
                "type": "string",
                "maxLength": 1024,
                "example": "If you have any questions, please contact us."
              },
              "link": {
                "description": "Link of invoice",
                "type": "string",
                "maxLength": 1024,
                "example": "https://quickbee.rootage.fr/share"
              },
            }
          },
          "GetQuotationList": {
            "type": "array",
            "items": {
              "$ref": "#/components/schemas/GetQuotation"
            }
          },
          "GetQuotation": {
            "properties": {
              "id": {
                "description": "Resource ID",
                "type": "int",
                "maxLength": 1024,
                "example": "1"
              },
              "name": {
                "description": "Name of quotation",
                "type": "string",
                "maxLength": 1024,
                "example": "196b7e02-88a6-4b40-8ae1-002f7e59fca0"
              },
              "client_id": {
                "description": "Client ID of quotation",
                "type": "string",
                "maxLength": 1024,
                "example": "1"
              },
              "client": {
                "description": "First name and last name of client",
                "type": "string",
                "maxLength": 1024,
                "example": "John Doe"
              },
              "issue_date": {
                "description": "Issue date of quotation",
                "type": "string",
                "maxLength": 1024,
                "example": "09/06/2024"
              },
              "infos": {
                "description": "Complémentary informations",
                "type": "string",
                "maxLength": 1024,
                "example": "If you have any questions, please contact us."
              },
              "link": {
                "description": "Link of quotation",
                "type": "string",
                "maxLength": 1024,
                "example": "https://quickbee.rootage.fr/share"
              },
            }
          },
        "Id": {
          "description": "Resource ID",
          "type": "integer",
          "format": "int64",
          "readOnly": true,
          "example": 1
        },
        "Error": {
          "required": [
            "code",
            "message"
          ],
          "properties": {
            "code": {
            "description": "Error code",
              "type": "int",
              "example": "404"
            },
            "message": {
            "description": "Error message",
              "type": "string",
              "example": "Resource not found."
            }
          }
        }
      },
      "parameters": {
        "Id": {
          "name": "id",
          "in": "path",
          "description": "Resource ID",
          "required": true,
          "schema": {
            "$ref": "#/components/schemas/Id"
          }
        }
      },
      "responses": {
        "NotFound": {
          "description": "The resource is not found.",
          "content": {
            "application/json": {
              "schema": {
                "$ref": "#/components/schemas/Error"
              }
            }
          }
        },
        "IllegalInput": {
          "description": "The input is invalid.",
          "content": {
            "application/json": {
              "schema": {
                "$ref": "#/components/schemas/Error"
              }
            }
          }
        }
      }
    }
  }