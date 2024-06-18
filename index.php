<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Reelz222z\CryptoExchange\User;
use Reelz222z\CryptoExchange\CoinMarketCapApiClient;
use Reelz222z\CryptoExchange\TransactionHistory;
use Reelz222z\CryptoExchange\Login;

$client = new Client();
$apiUrl = 'https://sandbox-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
$apiKey = 'YOUR_API_KEY';

$client = new Client();

function login(): User
{
    while (true) {
        $username = readline("Enter your username: ");
        $password = readline("Enter your password: ");
        $user = Login::authenticate($username, $password);

        if ($user) {
            return $user;
        } else {
            echo "Invalid username or password.\n";
        }
    }
}

$user = login();

echo "User found: " . $user->getName() . " with wallet balance: " . $user->getWallet()->getBalance() . " USD\n";

// Fetch top cryptocurrencies
$cryptoData = new CoinMarketCapApiClient($client, $apiUrl, $apiKey);
$topCryptos = $cryptoData->fetchTopCryptocurrencies();
echo "Top cryptocurrencies fetched successfully.\n";

function displayMenu(): void
{
    echo "Choose an option:\n";
    echo "1. List top cryptocurrencies\n";
    echo "2. Search cryptocurrency by symbol\n";
    echo "3. Buy cryptocurrency\n";
    echo "4. Sell cryptocurrency\n";
    echo "5. Display wallet state\n";
    echo "6. Display transaction history\n";
    echo "7. Exit\n";
}

while (true) {
    displayMenu();
    $choice = (int) readline("Enter your choice: ");

    switch ($choice) {
        case 1:
            echo "Available Cryptocurrencies:\n";
            foreach ($topCryptos as $crypto) {
                echo "Name: " . $crypto->getName() . " - Symbol: " . $crypto->getSymbol() . "\n";
                echo "Market Cap Dominance: " . $crypto->getQuote()->getMarketCapDominance() . "\n";
                echo "Price: $" . $crypto->getQuote()->getPrice() . "\n";
            }
            break;

        case 2:
            $symbol = readline("Enter the cryptocurrency symbol: ");
            $crypto = $cryptoData->getCryptocurrencyBySymbol($symbol);
            if ($crypto === null) {
                echo "Cryptocurrency not found.\n";
            } else {
                echo "Name: " . $crypto->getName() . "\n";
                echo "Symbol: " . $crypto->getSymbol() . "\n";
                echo "Market Cap: $" . $crypto->getQuote()->getMarketCap() . "\n";
                echo "Price: $" . $crypto->getQuote()->getPrice() . "\n";
                echo "Market Cap Dominance: " . $crypto->getQuote()->getMarketCapDominance() . "\n";
            }
            break;

        case 3:
            $symbol = readline("Enter the cryptocurrency symbol to buy: ");
            $crypto = $cryptoData->getCryptocurrencyBySymbol($symbol);
            if ($crypto === null) {
                echo "Cryptocurrency not found.\n";
            } else {
                echo "Name: " . $crypto->getName() . "\n";
                echo "Symbol: " . $crypto->getSymbol() . "\n";
                echo "Price: $" . $crypto->getQuote()->getPrice() . "\n";
                $choice = readline("Do you want to purchase this value? (yes/no): ");
                if (strtolower($choice) === 'yes') {
                    $amount = (float) readline("Enter the amount to buy: ");
                    $user->buyCryptocurrency($crypto, $amount);
                    TransactionHistory::addTransaction($user->getId(), $crypto->getSymbol(), $amount, 'buy', $crypto->getQuote()->getPrice());
                    echo "Bought $amount of " . $crypto->getName() . "\n";
                    User::saveUser($user);
                }
            }
            break;

        case 4:
            $portfolio = $user->getPortfolio();
            echo "Your Portfolio:\n";
            $i = 1;
            foreach ($portfolio as $symbol => $items) {
                $totalAmount = 0;
                foreach ($items as $item) {
                    $totalAmount += $item['amount'];
                }
                echo "$i. Symbol: $symbol, Amount: $totalAmount\n";
                $i++;
            }

            $choice = (int) readline("Enter the number of the cryptocurrency you want to sell: ");
            if ($choice > 0 && $choice <= count($portfolio)) {
                $symbol = array_keys($portfolio)[$choice - 1];
                $amount = (float) readline("Enter the amount to sell: ");
                $crypto = $cryptoData->getCryptocurrencyBySymbolSecond($symbol, $user);

                if ($crypto === null) {
                    echo "Cryptocurrency not found in your portfolio.\n";
                } else {
                    try {
                        $user->sellCryptocurrency($crypto, $amount);
                        TransactionHistory::addTransaction($user->getId(), $symbol, $amount, 'sell', $crypto->getQuote()->getPrice());
                        echo "Sold $amount of $symbol\n";
                        User::saveUser($user);
                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\n";
                    }
                }
            } else {
                echo "Invalid choice.\n";
            }
            break;

        case 5:
            echo "Current Wallet State:\n";
            echo "Balance: " . $user->getWallet()->getBalance() . " USD\n";
            echo "Portfolio: \n";
            foreach ($user->getPortfolio() as $symbol => $items) {
                $totalAmount = 0;
                foreach ($items as $item) {
                    $totalAmount += $item['amount'];
                }
                echo "$symbol: $totalAmount\n";
            }
            break;

        case 6:
            echo "Transaction History:\n";
            $transactions = TransactionHistory::getTransactions($user->getId());
            foreach ($transactions as $transaction) {
                echo $transaction['date'] . ": "
                    . $transaction['transaction_type'] . " "
                    . $transaction['amount']
                    . " of " . $transaction['asset']
                    . " at $" . $transaction['price']
                    . " each. Total: $" . $transaction['total'] . "\n";
            }
            break;

        case 7:
            exit;

        default:
            echo "Invalid choice. Please try again.\n";
            break;
    }
}
