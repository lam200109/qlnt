<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130063027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Attendance DROP FOREIGN KEY FK_UserAttendance');
        $this->addSql('ALTER TABLE Medicines DROP FOREIGN KEY medicines_ibfk_1');
        $this->addSql('ALTER TABLE PurchaseInvoiceDetails DROP FOREIGN KEY purchaseinvoicedetails_ibfk_1');
        $this->addSql('ALTER TABLE PurchaseInvoiceDetails DROP FOREIGN KEY purchaseinvoicedetails_ibfk_2');
        $this->addSql('ALTER TABLE PurchaseInvoices DROP FOREIGN KEY purchaseinvoices_ibfk_1');
        $this->addSql('ALTER TABLE RolePermissions DROP FOREIGN KEY rolepermissions_ibfk_1');
        $this->addSql('ALTER TABLE RolePermissions DROP FOREIGN KEY rolepermissions_ibfk_2');
        $this->addSql('ALTER TABLE Salary DROP FOREIGN KEY FK_UserSalary');
        $this->addSql('ALTER TABLE SalesInvoiceDetails DROP FOREIGN KEY salesinvoicedetails_ibfk_1');
        $this->addSql('ALTER TABLE SalesInvoiceDetails DROP FOREIGN KEY salesinvoicedetails_ibfk_2');
        $this->addSql('ALTER TABLE SalesInvoices DROP FOREIGN KEY salesinvoices_ibfk_1');
        $this->addSql('ALTER TABLE UserRoles DROP FOREIGN KEY userroles_ibfk_1');
        $this->addSql('ALTER TABLE UserRoles DROP FOREIGN KEY userroles_ibfk_2');
        $this->addSql('DROP TABLE Attendance');
        $this->addSql('DROP TABLE Customers');
        $this->addSql('DROP TABLE Distributors');
        $this->addSql('DROP TABLE Manufacturers');
        $this->addSql('DROP TABLE Medicines');
        $this->addSql('DROP TABLE Permissions');
        $this->addSql('DROP TABLE PurchaseInvoiceDetails');
        $this->addSql('DROP TABLE PurchaseInvoices');
        $this->addSql('DROP TABLE RolePermissions');
        $this->addSql('DROP TABLE Roles');
        $this->addSql('DROP TABLE Salary');
        $this->addSql('DROP TABLE SalesInvoiceDetails');
        $this->addSql('DROP TABLE SalesInvoices');
        $this->addSql('DROP TABLE UnitsOfMeasurement');
        $this->addSql('DROP TABLE UserRoles');
        $this->addSql('ALTER TABLE Users ADD user_id INT AUTO_INCREMENT NOT NULL, ADD roles JSON NOT NULL, DROP UserID, DROP Address, DROP CreatedDate, DROP Birthday, CHANGE Username username VARCHAR(50) NOT NULL, CHANGE Password password VARCHAR(255) NOT NULL, CHANGE Email email VARCHAR(100) NOT NULL, CHANGE Phone phone VARCHAR(20) NOT NULL, CHANGE Avatar avatar VARCHAR(255) NOT NULL, CHANGE FullName full_name VARCHAR(255) DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D5428AEDF85E0677 ON Users (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Attendance (AttendanceID INT NOT NULL, UserID INT DEFAULT NULL, Date DATE DEFAULT NULL, LoginTime TIME DEFAULT NULL, INDEX FK_UserAttendance (UserID), PRIMARY KEY(AttendanceID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Customers (CustomerID INT NOT NULL, Name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Phone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Status INT DEFAULT NULL, Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(CustomerID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Distributors (DistributorID INT NOT NULL, DistributorName VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Phone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Fax VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Note TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(DistributorID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Manufacturers (ManufacturerID INT NOT NULL, Company VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Phone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, SupplyPrice NUMERIC(10, 2) DEFAULT NULL, Status INT DEFAULT NULL, Gmail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(ManufacturerID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Medicines (MedicineID INT AUTO_INCREMENT NOT NULL, Name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, GenericName VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, SKU VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Concentration VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Category VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, ManufacturerID INT DEFAULT NULL, Price NUMERIC(10, 2) DEFAULT NULL, ManufacturerPrice NUMERIC(10, 2) DEFAULT NULL, InStock INT DEFAULT NULL, ExpirationDate DATE DEFAULT NULL, Status VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, INDEX ManufacturerID (ManufacturerID), PRIMARY KEY(MedicineID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Permissions (PermissionID INT NOT NULL, PermissionName VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(PermissionID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE PurchaseInvoiceDetails (PurchaseInvoiceDetailID INT AUTO_INCREMENT NOT NULL, PurchaseInvoiceID INT DEFAULT NULL, MedicineID INT DEFAULT NULL, Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Price NUMERIC(10, 2) DEFAULT NULL, Quantity INT DEFAULT NULL, Total NUMERIC(10, 2) DEFAULT NULL, CreatedDate DATE DEFAULT NULL, INDEX PurchaseInvoiceID (PurchaseInvoiceID), INDEX MedicineID (MedicineID), PRIMARY KEY(PurchaseInvoiceDetailID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE PurchaseInvoices (PurchaseInvoiceID INT AUTO_INCREMENT NOT NULL, DistributorID INT DEFAULT NULL, Expense NUMERIC(10, 2) DEFAULT NULL, Date DATE DEFAULT NULL, ExpenseType VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Amount NUMERIC(10, 2) DEFAULT NULL, INDEX DistributorID (DistributorID), PRIMARY KEY(PurchaseInvoiceID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE RolePermissions (RolePermissionsID INT NOT NULL, RoleID INT DEFAULT NULL, PermissionID INT DEFAULT NULL, INDEX RoleID (RoleID), INDEX PermissionID (PermissionID), PRIMARY KEY(RolePermissionsID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Roles (RoleID INT NOT NULL, RoleName VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(RoleID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE Salary (SalaryID INT NOT NULL, UserID INT DEFAULT NULL, Date DATE DEFAULT NULL, TotalEarnings NUMERIC(10, 2) DEFAULT NULL, TotalWorkingDays INT DEFAULT NULL, INDEX FK_UserSalary (UserID), PRIMARY KEY(SalaryID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE SalesInvoiceDetails (SalesInvoiceDetailID INT NOT NULL, SalesInvoiceID INT DEFAULT NULL, MedicineID INT DEFAULT NULL, Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Price NUMERIC(10, 2) DEFAULT NULL, Quantity INT DEFAULT NULL, Total NUMERIC(10, 2) DEFAULT NULL, CreatedDate DATE DEFAULT NULL, INDEX SalesInvoiceID (SalesInvoiceID), INDEX MedicineID (MedicineID), PRIMARY KEY(SalesInvoiceDetailID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE SalesInvoices (SalesInvoiceID INT NOT NULL, CustomerID INT DEFAULT NULL, Date DATE DEFAULT NULL, IncomeType VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Amount NUMERIC(10, 2) DEFAULT NULL, INDEX CustomerID (CustomerID), PRIMARY KEY(SalesInvoiceID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE UnitsOfMeasurement (UnitID INT NOT NULL, UnitName VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(UnitID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE UserRoles (UserRolesID INT NOT NULL, UserID INT DEFAULT NULL, RoleID INT DEFAULT NULL, INDEX UserID (UserID), INDEX RoleID (RoleID), PRIMARY KEY(UserRolesID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE Attendance ADD CONSTRAINT FK_UserAttendance FOREIGN KEY (UserID) REFERENCES Users (UserID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE Medicines ADD CONSTRAINT medicines_ibfk_1 FOREIGN KEY (ManufacturerID) REFERENCES Manufacturers (ManufacturerID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE PurchaseInvoiceDetails ADD CONSTRAINT purchaseinvoicedetails_ibfk_1 FOREIGN KEY (PurchaseInvoiceID) REFERENCES PurchaseInvoices (PurchaseInvoiceID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE PurchaseInvoiceDetails ADD CONSTRAINT purchaseinvoicedetails_ibfk_2 FOREIGN KEY (MedicineID) REFERENCES Medicines (MedicineID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE PurchaseInvoices ADD CONSTRAINT purchaseinvoices_ibfk_1 FOREIGN KEY (DistributorID) REFERENCES Distributors (DistributorID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE RolePermissions ADD CONSTRAINT rolepermissions_ibfk_1 FOREIGN KEY (RoleID) REFERENCES Roles (RoleID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE RolePermissions ADD CONSTRAINT rolepermissions_ibfk_2 FOREIGN KEY (PermissionID) REFERENCES Permissions (PermissionID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE Salary ADD CONSTRAINT FK_UserSalary FOREIGN KEY (UserID) REFERENCES Users (UserID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE SalesInvoiceDetails ADD CONSTRAINT salesinvoicedetails_ibfk_1 FOREIGN KEY (SalesInvoiceID) REFERENCES SalesInvoices (SalesInvoiceID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE SalesInvoiceDetails ADD CONSTRAINT salesinvoicedetails_ibfk_2 FOREIGN KEY (MedicineID) REFERENCES Medicines (MedicineID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE SalesInvoices ADD CONSTRAINT salesinvoices_ibfk_1 FOREIGN KEY (CustomerID) REFERENCES Customers (CustomerID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE UserRoles ADD CONSTRAINT userroles_ibfk_1 FOREIGN KEY (UserID) REFERENCES Users (UserID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE UserRoles ADD CONSTRAINT userroles_ibfk_2 FOREIGN KEY (RoleID) REFERENCES Roles (RoleID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE Users MODIFY user_id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D5428AEDF85E0677 ON Users');
        $this->addSql('DROP INDEX `PRIMARY` ON Users');
        $this->addSql('ALTER TABLE Users ADD UserID INT NOT NULL, ADD Address VARCHAR(45) DEFAULT NULL, ADD CreatedDate DATETIME DEFAULT NULL, ADD Birthday DATETIME DEFAULT NULL, DROP user_id, DROP roles, CHANGE username Username VARCHAR(50) DEFAULT NULL, CHANGE password Password VARCHAR(255) DEFAULT NULL, CHANGE email Email VARCHAR(100) DEFAULT NULL, CHANGE phone Phone VARCHAR(20) DEFAULT NULL, CHANGE avatar Avatar VARCHAR(255) DEFAULT NULL, CHANGE full_name FullName VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE Users ADD PRIMARY KEY (UserID)');
    }
}
