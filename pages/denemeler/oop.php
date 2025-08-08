<?php
// ... Abstract ve somut sınıflarınız burada aynen kalacak ...

abstract class Payment{
    protected $amount;
    protected $status = "pending";

    public function __construct($amount){
        $this->amount = $amount;
    }
    // processPayment metodu parametre almıyor
    abstract public function processPayment();

    public function getStatus()
    {
        return $this->status;
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }
}

class CreditCardPayment extends Payment{

    public function __construct($amount)
    {
        parent::__construct($amount);
    }
    public function processPayment()
    {
        echo "Kredi kartı ile {$this->amount} tutarında ödeme işlemi başlatıldı.<br>";
        $this->status = 'paid';
    }
}

class BankTransferPayment extends Payment {
    public function __construct($amount)
    {
        parent::__construct($amount);
    }

    // Doğru implementasyon: Parametresiz ve gövde dolu
    public function processPayment()
    {
        echo "Havale ile {$this->amount} tutarında ödeme işlemi yapmanız gerekiyor.<br>";
        // Bu ödeme türünde status varsayılan olarak 'pending' kalır
    }
}


// Doğru Kullanım:

// Kredi Kartı Ödemesi
$creditCardPayment = new CreditCardPayment("1500 TL");
$creditCardPayment->processPayment(); // Ödeme işlemini başlatıyoruz
echo "Ödeme Durumu: " . $creditCardPayment->getStatus() . "<br>";
echo "Ödeme Yapıldı mı? " . ($creditCardPayment->isPaid() ? 'Evet' : 'Hayır') . "<br>";
echo "<hr>";

// Havale Ödemesi
$bankTransferPayment = new BankTransferPayment("2500 TL");
$bankTransferPayment->processPayment(); // Ödeme işlemini başlatıyoruz
echo "Ödeme Durumu: " . $bankTransferPayment->getStatus() . "<br>";
echo "Ödeme Yapıldı mı? " . ($bankTransferPayment->isPaid() ? 'Evet' : 'Hayır') . "<br>";

?>