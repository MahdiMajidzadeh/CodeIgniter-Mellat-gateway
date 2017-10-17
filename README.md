# CodeIgniter-Mellat-gateway


Codeigniter library for Iranian bank, Mellat.

## how to install

Copy `Mellat.php` and `nusoap` directory to `application/libraries` of your own project.

## how to use

First, load library:
```
$this->load->library('mellat');
```
second, set options:
```
$this->mellat->set_options(
    $terminal = '',
    $username = '',
    $password = '',
    $amount = 0,
    $order = 0,
    $callback = ''
    );
```

For sending user to gateway:
```
$this->mellat->send();
```

For verify user payment:
```
$this->mellat->verify_payment($SaleOrderId, $SaleReferenceId)
```


## Contributor
- Mahdi Majidzadeh ([github](https://github.com/MahdiMajidzadeh))
