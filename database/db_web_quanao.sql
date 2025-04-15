-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 10:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_web_quanao`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_details`
--

CREATE TABLE `cart_details` (
  `cart_detail_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Áo thun'),
(2, 'Quần'),
(3, 'Áo Sơ Mi'),
(4, 'Áo Polo'),
(5, 'Áo Khoác'),
(6, 'Quần lót');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `color_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`color_id`, `name`, `hex_code`) VALUES
(1, 'Đen', '#282A2B'),
(2, 'Be', '#DBD1BC'),
(3, 'Nâu', '#90713B'),
(4, 'Xám nhạt', '#9FA9A9'),
(5, 'Hồng nhạt', '#D07771'),
(6, 'Xanh rêu', '#95987B'),
(7, 'Xanh biển đậm', '#4F5C7C'),
(8, 'Trắng', '#F5F1E6'),
(9, 'Đỏ', '#A5051D'),
(10, 'Olive', '#59564F'),
(11, 'Xanh biển nhạt', '#387EA0'),
(12, 'Navy', '#3C4252'),
(13, 'Rượu vang', '#391D2B'),
(14, 'Be đậm', '#B58F6C');

-- --------------------------------------------------------

--
-- Table structure for table `importreceipt`
--

CREATE TABLE `importreceipt` (
  `ImportReceipt_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `importreceipt`
--

INSERT INTO `importreceipt` (`ImportReceipt_id`, `supplier_id`, `user_id`, `total_price`, `created_at`) VALUES
(1, 20, 3, 3346000.00, '2025-04-12 18:57:21'),
(2, 12, 3, 1852000.00, '2025-04-13 19:01:31'),
(3, 10, 3, 3233000.00, '2025-04-17 10:02:26'),
(4, 2, 3, 1982000.00, '2025-04-18 12:34:56'),
(5, 19, 3, 192000.00, '2025-04-19 19:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `importreceipt_details`
--

CREATE TABLE `importreceipt_details` (
  `ImportReceipt_details_id` int(11) NOT NULL,
  `ImportReceipt_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `rate_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_method_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `name`) VALUES
(1, 'Thanh toán khi nhận hàng (COD)'),
(2, 'Chuyển khoản ngân hàng'),
(3, 'Thanh toán qua ví Momo');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`) VALUES
(1, 'Quản lý sản phẩm'),
(2, 'Quản lý đơn hàng'),
(3, 'Quản lý người dùng'),
(4, 'Quản lý đơn nhập'),
(5, 'Xem báo cáo'),
(6, 'Quản lý quyền');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `rating_avg` float DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0,
  `price_sale` decimal(12,2) DEFAULT NULL,
  `pttg` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `category_id`, `price`, `rating_avg`, `rating_count`, `sold_count`, `price_sale`, `pttg`) VALUES
(1, 'Áo polo nam dệt kim tay ngắn form regular', 'Khám phá phong cách lịch lãm và thoải mái tuyệt đối với chiếc áo polo nam dệt kim tay ngắn form regular – lựa chọn hoàn hảo cho mọi chàng trai hiện đại. Được làm từ chất liệu cotton cao cấp pha sợi spandex, áo mang đến cảm giác mềm mại, thoáng khí nhưng vẫn giữ được form dáng chuẩn sau nhiều lần giặt. Thiết kế tinh tế với cổ áo bo gân, tay áo ôm nhẹ tạo điểm nhấn năng động, dễ dàng phối cùng quần jean, kaki hoặc short cho nhiều phong cách từ công sở đến dạo phố.\r\n\r\n✔️ Chất vải dày dặn – không bai xù\r\n✔️ Đường may chắc chắn – tỉ mỉ đến từng chi tiết\r\n✔️ Dễ phối đồ – phù hợp mọi hoàn cảnh\r\n✔️ Size đa dạng – phù hợp mọi vóc dáng\r\n\r\nSở hữu ngay để nâng tầm phong cách cùng sự thoải mái suốt cả ngày!', 1, 540000.00, 0, 0, 0, 702000.00, 30.00),
(2, 'Áo polo nam phối màu tay và cổ form fitted', 'Tạo điểm nhấn cá tính trong tủ đồ của bạn với áo polo nam phối màu tay và cổ form fitted – thiết kế vừa vặn tôn dáng, chuẩn xu hướng thời trang hiện đại. Phần cổ áo và tay áo phối màu tinh tế mang lại cảm giác trẻ trung, năng động nhưng vẫn giữ được vẻ lịch lãm đặc trưng của dòng áo polo cổ điển.\r\n\r\nÁo được may từ chất liệu cotton co giãn 4 chiều, cho cảm giác mặc thoải mái, thấm hút mồ hôi tốt – lý tưởng cho các hoạt động thường ngày hay khi di chuyển ngoài trời. Đường may ôm nhẹ phần vai và thân, tôn lên vóc dáng săn chắc, phù hợp với các chàng trai yêu thích sự gọn gàng và phong cách.\r\n\r\n✔️ Form fitted – ôm nhẹ body, tôn dáng\r\n✔️ Cổ & tay áo phối màu nổi bật – tạo điểm nhấn phong cách\r\n✔️ Vải mềm, thoáng khí – mặc thoải mái cả ngày\r\n✔️ Phối đồ dễ dàng – phù hợp từ công sở đến cafe cuối tuần\r\n\r\nMột chiếc polo không chỉ đơn thuần là áo – mà là tuyên ngôn thời trang của bạn!', 4, 490000.00, 0, 0, 0, 637000.00, 30.00),
(3, 'Áo sơ mi nam tay dài vải Nano Non Iron fitted', 'Đẳng cấp đến từ sự đơn giản – áo sơ mi nam tay dài vải Nano Non Iron form fitted là lựa chọn lý tưởng cho những quý ông hiện đại, bận rộn nhưng vẫn yêu cầu cao về phong cách. Với chất liệu vải Nano công nghệ cao, áo sở hữu khả năng chống nhăn vượt trội, giữ form chuẩn suốt cả ngày mà không cần ủi – giúp bạn tiết kiệm thời gian mà vẫn luôn chỉn chu.\r\n\r\nThiết kế form fitted ôm gọn vừa vặn, tôn dáng và mang lại sự thoải mái tối đa nhờ độ co giãn nhẹ. Cổ áo cứng cáp, tay áo sắc sảo, đường may tinh tế – chiếc sơ mi này phù hợp từ văn phòng công sở đến những buổi họp quan trọng hay sự kiện cần sự chuyên nghiệp.\r\n\r\n✔️ Chống nhăn – không cần ủi, mặc là phẳng\r\n✔️ Form fitted – ôm dáng vừa vặn, hiện đại\r\n✔️ Chất vải Nano mềm mại, thoáng khí, không gây bí\r\n✔️ Phối đồ linh hoạt – dễ dàng kết hợp với quần tây, jean hoặc kaki\r\n\r\nSở hữu ngay để nâng tầm phong thái chuyên nghiệp và tinh tế mỗi ngày!', 3, 540000.00, 0, 0, 0, 702000.00, 30.00),
(4, 'Áo khoác gile nam chần bông có túi form regular', 'Giữ ấm đầy phong cách với áo khoác gile nam chần bông có túi form regular – thiết kế không tay hiện đại, thích hợp cho những ngày se lạnh nhưng vẫn cần sự linh hoạt trong di chuyển. Áo được chần bông nhẹ, êm ái, giúp giữ nhiệt tốt mà không gây nặng nề hay cồng kềnh.\r\n\r\nChất liệu vải dù chống nước nhẹ, dễ vệ sinh, kết hợp với lớp lót mềm mại bên trong mang đến cảm giác ấm áp và thoải mái khi mặc. Form regular vừa vặn với hầu hết vóc dáng, không kén người mặc. Hai túi tiện lợi hai bên, có thể giữ tay ấm hoặc đựng các vật dụng cá nhân như điện thoại, ví, tai nghe,...\r\n\r\n✔️ Chần bông giữ ấm – nhẹ, không dày cộm\r\n✔️ Kiểu dáng gọn gàng – dễ phối áo thun, sơ mi hoặc hoodie bên trong\r\n✔️ Túi tiện lợi – vừa thời trang vừa thực dụng\r\n✔️ Form regular – phù hợp với nhiều vóc dáng và phong cách\r\n\r\nChiếc gile lý tưởng để bạn kết hợp linh hoạt từ đi làm, đi chơi đến dạo phố mỗi ngày!', 5, 785000.00, 0, 0, 0, 1020500.00, 30.00),
(5, 'Quần short nỉ nam cotton có hình thêu relax', 'Thoải mái, trẻ trung và đậm chất riêng – quần short nỉ nam cotton có hình thêu relax là lựa chọn hoàn hảo cho những ngày cuối tuần thảnh thơi hay các buổi tập thể thao nhẹ nhàng. Được làm từ chất liệu cotton nỉ mềm mại, sản phẩm mang đến cảm giác mặc cực kỳ dễ chịu, thoáng khí và thân thiện với làn da.\r\n\r\nThiết kế form suông vừa vặn, phối cùng phần hình thêu tinh tế ở ống quần tạo điểm nhấn cá tính mà không làm mất đi sự đơn giản, dễ phối đồ. Lưng quần có dây rút co giãn, giúp bạn điều chỉnh linh hoạt theo vóc dáng. Dù là đi chơi, ở nhà hay đi dạo phố – chiếc quần short này luôn mang lại vẻ năng động và tự tin cho bạn.\r\n\r\n✔️ Chất nỉ cotton dày dặn – thoáng, mềm, thấm hút mồ hôi\r\n✔️ Hình thêu relax – tinh tế, nổi bật nhưng không lòe loẹt\r\n✔️ Lưng thun co giãn – thoải mái mọi chuyển động\r\n✔️ Phối đồ cực dễ – kết hợp áo thun, tanktop hay hoodie đều hợp\r\n\r\nLà chiếc quần short “must-have” trong tủ đồ nam mỗi khi bạn muốn vừa thoải mái vừa trông thật cool!', 2, 422000.00, 0, 0, 0, 548600.00, 30.00),
(6, 'Áo thun tay ngắn nam S.café in.Boxy', 'Nâng tầm trải nghiệm mặc hằng ngày với áo thun tay ngắn nam S.Café in.Boxy – thiết kế hiện đại kết hợp cùng chất liệu đột phá từ công nghệ S.Café. Với thành phần cafe tái chế tích hợp trong sợi vải, áo sở hữu khả năng khử mùi, thoáng khí và khô nhanh vượt trội, mang lại cảm giác thoải mái suốt cả ngày dài.\r\n\r\nPhom dáng boxy hiện đại, rộng rãi và cực kỳ trẻ trung – phù hợp cho những ai yêu thích sự năng động, phá cách nhưng vẫn tinh tế. Thiết kế in tối giản trước ngực tạo điểm nhấn nhẹ nhàng, dễ phối với nhiều phong cách từ basic đến streetwear.\r\n\r\n✔️ Vải S.Café – công nghệ xanh, thân thiện môi trường\r\n✔️ Khử mùi – thoáng khí – thấm hút mồ hôi tốt\r\n✔️ Form boxy – phóng khoáng, che dáng tốt\r\n✔️ Dễ phối đồ – phù hợp đi học, đi chơi, đi làm\r\n\r\nLựa chọn lý tưởng cho chàng trai hiện đại – sống xanh, mặc chất!', 1, 569000.00, 0, 0, 0, 739700.00, 30.00),
(7, 'Áo thun tay ngắn nam S.Café gắn icon.Loose', 'Thoáng mát – bền bỉ – đậm cá tính: Áo thun tay ngắn nam S.Café gắn icon.Loose là lựa chọn hoàn hảo cho những ngày năng động và cần sự tự do trong từng chuyển động. Ứng dụng công nghệ vải S.Café – làm từ bã cà phê tái chế – áo sở hữu khả năng khử mùi, khô nhanh và chống tia UV, giúp bạn luôn tự tin và dễ chịu dù hoạt động cả ngày dài.\r\n\r\nThiết kế form loose rộng rãi, trẻ trung, mang hơi hướng streetwear thoải mái, dễ phối đồ. Điểm nhấn là chi tiết icon gắn trước ngực hoặc tay áo, tạo điểm nhấn tinh tế mà không rườm rà. Dù đi học, đi chơi hay đơn giản là dạo phố, chiếc áo này luôn đồng hành cùng phong cách tự do và năng động của bạn.\r\n\r\n✔️ Vải công nghệ S.Café – thân thiện môi trường, hiệu suất cao\r\n✔️ Khử mùi – chống UV – thoáng mát cả ngày\r\n✔️ Phom loose rộng rãi – dễ chịu, che dáng tốt\r\n✔️ Chi tiết icon gắn tinh tế – nhấn nhẹ cá tính riêng\r\n\r\nMột chiếc áo basic nhưng không nhàm chán – mặc nhẹ, sống chất!', 1, 499000.00, 0, 0, 0, 648700.00, 30.00),
(8, 'Áo sơ mi oxford nam tay dài fitted - Smartshirt', 'Tinh tế trong từng đường nét – áo sơ mi Oxford nam tay dài form fitted Smartshirt là biểu tượng của sự chỉn chu và hiện đại dành cho nam giới. Được may từ vải Oxford dày dặn nhưng vẫn thoáng mát, áo mang đến cảm giác mặc dễ chịu cả ngày, giữ form chuẩn và ít nhăn – lý tưởng cho môi trường công sở hay các dịp cần lịch sự.\r\n\r\nThiết kế form fitted ôm nhẹ cơ thể, tôn dáng mà vẫn đảm bảo sự thoải mái khi vận động. Đặc biệt, chi tiết Smartshirt mang hàm ý thiết kế thông minh – từ chất liệu, đường may cho đến cử động đều được tối ưu, giúp bạn tự tin và linh hoạt trong mọi hoàn cảnh.\r\n\r\n✔️ Vải Oxford cao cấp – bền màu, đứng dáng, thoáng khí\r\n✔️ Thiết kế Smartshirt – hiện đại, tối ưu cử động\r\n✔️ Form fitted – gọn gàng, tôn dáng lịch thiệp\r\n✔️ Phù hợp đi làm, gặp đối tác, dạo phố hay hẹn hò\r\n\r\nMột chiếc sơ mi thông minh – dành cho người đàn ông tinh tế và biết chọn đúng phong cách.', 3, 471000.00, 0, 0, 0, 612300.00, 30.00),
(9, 'Quần short denim nam form straight', 'Năng động, khỏe khoắn và không bao giờ lỗi mốt – quần short denim nam form straight là item không thể thiếu trong tủ đồ của mọi chàng trai hiện đại. Thiết kế ống suông vừa vặn, giúp tạo cảm giác cân đối và thoải mái khi mặc, phù hợp với nhiều vóc dáng.\r\n\r\nĐược làm từ chất liệu denim cao cấp, bề mặt mềm, độ bền cao và dễ giặt sạch. Phong cách tối giản nhưng vẫn nam tính, quần có thể kết hợp linh hoạt với áo thun, polo hay sơ mi – từ phong cách đời thường đến streetwear trẻ trung.\r\n\r\n✔️ Form straight – ống suông gọn gàng, dễ mặc\r\n✔️ Denim dày dặn – bền màu, không bai nhão\r\n✔️ Túi tiện lợi – tăng tính ứng dụng\r\n✔️ Phối đồ linh hoạt – phù hợp nhiều phong cách\r\n\r\nChiếc quần lý tưởng để bạn tự tin xuống phố – thoải mái nhưng vẫn cực “chất”!', 2, 441000.00, 0, 0, 0, 573300.00, 30.00),
(10, 'Áo polo nam tay ngắn miếng patch ngực', 'Đơn giản nhưng nổi bật – áo polo nam tay ngắn có miếng patch ngực mang đến sự kết hợp hoàn hảo giữa phong cách cổ điển và điểm nhấn hiện đại. Thiết kế cổ bẻ truyền thống, tay bo gọn gàng kết hợp cùng miếng patch thêu/logo ngực cá tính, giúp chiếc áo trở nên khác biệt mà vẫn giữ được vẻ lịch sự vốn có của dòng polo.\r\n\r\nChất liệu cotton co giãn nhẹ, thấm hút mồ hôi tốt, đảm bảo sự thoải mái khi mặc suốt cả ngày. Form áo vừa vặn (regular/slim) tôn dáng, dễ phối đồ với mọi loại quần – từ jeans, kaki đến short.\r\n\r\n✔️ Thiết kế polo tay ngắn – trẻ trung, năng động\r\n✔️ Miếng patch ngực – tạo điểm nhấn nổi bật\r\n✔️ Chất vải cotton mềm mại, thoáng khí\r\n✔️ Dễ mặc – dễ phối – phù hợp nhiều hoàn cảnh\r\n\r\nChiếc áo polo cho chàng trai hiện đại – tinh tế trong từng chi tiết nhỏ!', 4, 441000.00, 0, 0, 0, 573300.00, 30.00),
(11, 'Áo sơ mi nam tay ngắn kẻ sọc cotton fitted', 'Lịch lãm, gọn gàng và không bao giờ lỗi thời – áo sơ mi nam tay ngắn kẻ sọc cotton form fitted là lựa chọn hoàn hảo cho những chàng trai yêu thích phong cách nam tính nhưng vẫn thoải mái trong từng chuyển động. Thiết kế kẻ sọc tinh tế giúp tạo hiệu ứng thon gọn, tôn dáng tối đa.\r\n\r\nChất liệu cotton mềm mại, thoáng khí và thấm hút mồ hôi tốt – lý tưởng cho những ngày hè oi bức hay các hoạt động ngoài trời. Dáng fitted ôm nhẹ, ôn hoà giữa form slim và regular, giúp giữ sự chỉn chu mà không bị gò bó. Dễ phối cùng quần tây, jeans hoặc short cho nhiều phong cách từ đi làm đến đi chơi.\r\n\r\n✔️ Vải cotton thoáng mát – dễ chịu cả ngày dài\r\n✔️ Họa tiết kẻ sọc trẻ trung – tôn dáng, thanh lịch\r\n✔️ Form fitted – vừa vặn, không quá ôm\r\n✔️ Phối đồ linh hoạt – công sở, dạo phố đều hợp\r\n\r\nChiếc sơ mi không thể thiếu cho mùa hè – mặc lên là sáng cả outfit!', 3, 399000.00, 0, 0, 0, 518700.00, 30.00),
(12, 'Áo sơ mi nam dài tay cotton cổ trụ form fitted', 'Tinh tế, tối giản và khác biệt – áo sơ mi nam dài tay cotton cổ trụ form fitted mang đến làn gió mới cho phong cách công sở và thời trang thường nhật. Thiết kế cổ trụ hiện đại, thay thế cổ áo truyền thống bằng đường bo gọn gàng, tạo nên vẻ ngoài trẻ trung, năng động mà vẫn giữ sự lịch thiệp cần có.\r\n\r\nÁo sử dụng chất liệu cotton cao cấp, mềm mại, thấm hút mồ hôi tốt, cho cảm giác mặc dễ chịu cả ngày. Form fitted ôm nhẹ cơ thể, tôn dáng mà vẫn thoải mái khi vận động. Phù hợp để phối cùng quần jeans, kaki hoặc quần tây – linh hoạt từ đi làm đến đi chơi.\r\n\r\n✔️ Cổ trụ hiện đại – phá cách nhẹ, tinh tế\r\n✔️ Vải cotton mềm mịn – thoáng mát, co giãn nhẹ\r\n✔️ Form fitted – gọn gàng, tôn dáng nam tính\r\n✔️ Dễ phối đồ – phù hợp cả phong cách casual lẫn smart\r\n\r\nChiếc sơ mi dành cho những người đàn ông yêu sự khác biệt nhưng vẫn luôn chỉn chu.', 3, 499000.00, 0, 0, 0, 648700.00, 30.00),
(13, 'Áo khoác dày tay chần bông có túi form loose', 'Đối đầu thời tiết lạnh với phong cách thật chất – áo khoác dày tay chần bông có túi form loose là lựa chọn hoàn hảo cho những ngày đông lạnh nhưng vẫn muốn giữ được vẻ ngoài năng động, cá tính. Thiết kế tay áo chần bông giúp giữ ấm tối ưu, đồng thời tạo hiệu ứng layer nổi bật, mang lại vẻ ngoài khỏe khoắn và thời thượng.\r\n\r\nVới form loose rộng rãi, áo mang đến cảm giác mặc thoải mái, dễ layering cùng hoodie, sweater hay áo len bên trong. Hai túi lớn phía trước tiện lợi cho việc giữ ấm tay hoặc đựng các vật dụng cá nhân. Chất liệu vải dày dặn, giữ nhiệt tốt, thích hợp cho cả đi học, đi làm, hoặc du lịch mùa lạnh.\r\n\r\n✔️ Chần bông tay – giữ ấm tốt, tạo điểm nhấn cá tính\r\n✔️ Chất liệu dày – chắn gió, giữ nhiệt hiệu quả\r\n✔️ Form loose – phóng khoáng, thoải mái vận động\r\n✔️ Có túi tiện lợi – thực dụng và thời trang\r\n\r\nChiếc áo khoác lý tưởng cho mùa lạnh – ấm áp, năng động và cực dễ phối đồ.', 5, 981000.00, 0, 0, 0, 1275300.00, 30.00),
(14, 'Áo sweater nam cổ tròn point label', 'Đơn giản mà khác biệt – áo sweater nam cổ tròn point label là lựa chọn lý tưởng cho những ngày se lạnh cần sự ấm áp nhẹ nhàng mà vẫn thời trang. Thiết kế cổ tròn cổ điển, tay dài bo gấu kết hợp với chi tiết point label trước ngực hoặc thân áo, tạo điểm nhấn tinh tế, tăng chiều sâu cho outfit mà không cần quá phô trương.\r\n\r\nÁo được làm từ chất vải nỉ bông mềm mại, dày vừa phải, giữ ấm tốt nhưng không gây bí bách. Phom dáng regular vừa vặn, dễ phối với quần jeans, jogger hoặc short – từ phong cách năng động đến tối giản đều', 5, 540000.00, 0, 0, 0, 702000.00, 30.00),
(15, 'Áo thun nam cotton tay ngắn trơn regular', 'Tối giản nhưng không đơn điệu – áo thun nam cotton tay ngắn trơn form regular là item cơ bản mà bất kỳ tủ đồ nam nào cũng nên có. Với thiết kế trơn màu dễ phối, cổ tròn cổ điển và phom regular thoải mái, chiếc áo mang lại sự linh hoạt tuyệt đối trong mọi hoàn cảnh – từ mặc ở nhà, đi chơi đến phối layer cùng áo khoác.\r\n\r\nChất liệu 100% cotton cao cấp, mềm mại, thấm hút mồ hôi tốt, không gây bí da kể cả khi hoạt động ngoài trời hay trong thời tiết nóng. Đường may chắc chắn, bền dáng sau nhiều lần giặt, giữ cho áo luôn mới và form đẹp.\r\n\r\n✔️ Cotton 100% – mềm, mát, dễ chịu suốt cả ngày\r\n✔️ Form regular – thoải mái, phù hợp mọi vóc dáng\r\n✔️ Thiết kế trơn – dễ phối với bất kỳ outfit nào\r\n✔️ Cổ tròn cơ bản – trẻ trung, không lỗi mốt\r\n\r\nChiếc áo “basic” mà bạn sẽ muốn mặc đi mặc lại – càng đơn giản, càng dễ nổi bật.', 1, 343000.00, 0, 0, 0, 445900.00, 30.00),
(16, 'Áo thun tay ngắn nam S.Café gắn icon.Fitted', 'Mặc đẹp mỗi ngày và thân thiện với môi trường – áo thun tay ngắn nam S.Café gắn icon.Fitted là sự kết hợp hoàn hảo giữa thời trang tối giản và công nghệ vải xanh bền vững. Chất liệu vải ứng dụng công nghệ S.Café từ bã cà phê tái chế, giúp áo sở hữu khả năng khử mùi, khô nhanh và chống tia UV, cực kỳ lý tưởng cho các chàng trai năng động.\r\n\r\nThiết kế form fitted ôm nhẹ, tôn dáng gọn gàng mà vẫn đảm bảo sự thoải mái. Điểm nhấn là chi tiết icon nhỏ gọn được gắn khéo léo ở ngực hoặc tay áo, giúp chiếc áo trở nên cá tính và khác biệt trong sự tối giản.\r\n\r\n✔️ Vải S.Café thân thiện môi trường – thoáng khí, khử mùi hiệu quả\r\n✔️ Phom dáng fitted – tôn body, hiện đại\r\n✔️ Thiết kế gắn icon – nhấn nhẹ cá tính, không cần cầu kỳ\r\n✔️ Dễ phối đồ – phù hợp cả mặc thường ngày lẫn thể thao nhẹ\r\n\r\nSự lựa chọn của chàng trai hiện đại: mặc đẹp, sống xanh, cảm thấy thoải mái cả ngày dài.', 1, 471000.00, 0, 0, 0, 612300.00, 30.00),
(17, 'Áo polo nam premium 100% cotton phối sọc form fitt', 'Đẳng cấp đến từ sự tinh tế – áo polo nam premium 100% cotton phối sọc form fitted là lựa chọn lý tưởng cho những quý ông hiện đại yêu thích phong cách gọn gàng, lịch thiệp nhưng vẫn thoải mái. Sản phẩm được may từ 100% cotton cao cấp, mềm mịn, thoáng khí, thân thiện với làn da và giữ form cực tốt.\r\n\r\nThiết kế phối sọc tinh tế chạy dọc hoặc ngang tùy phiên bản, giúp tạo điểm nhấn bắt mắt mà không làm mất đi sự tối giản sang trọng. Phom fitted ôm nhẹ, tôn dáng và giúp tổng thể trở nên gọn gàng, hiện đại hơn. Phù hợp cho cả đi làm, gặp gỡ đối tác, hoặc những buổi dạo phố, cafe cuối tuần.\r\n\r\n✔️ 100% cotton cao cấp – mềm mát, thấm hút vượt trội\r\n✔️ Form fitted – ôm nhẹ, tôn dáng sang trọng\r\n✔️ Thiết kế phối sọc thanh lịch – không phô trương nhưng nổi bật\r\n✔️ Cổ polo chuẩn – giữ form, không bị bai nhão\r\n\r\nChiếc polo cao cấp giúp bạn nâng tầm phong cách – tinh gọn, lịch lãm, đầy khí chất.', 4, 589000.00, 0, 0, 0, 765700.00, 30.00),
(18, 'Áo polo nam tay ngắn cotton sọc ngang form loose', 'Tự do thể hiện phong cách cá nhân với áo polo nam tay ngắn cotton sọc ngang form loose – lựa chọn lý tưởng cho những ai yêu thích sự thoải mái, trẻ trung mà vẫn giữ nét chỉn chu của chiếc áo cổ bẻ truyền thống. Thiết kế sọc ngang nổi bật, mang lại cảm giác năng động, khỏe khoắn và phù hợp với nhiều phong cách từ basic đến streetwear.\r\n\r\nChất liệu cotton mềm mại, thấm hút tốt, tạo cảm giác dễ chịu cả ngày dài. Phom áo loose rộng rãi, phù hợp với nhiều vóc dáng, dễ mix cùng jeans, quần short hay kaki để đi chơi, dạo phố hoặc hoạt động ngoài trời.\r\n\r\n✔️ Vải cotton cao cấp – thoáng khí, thấm hút mồ hôi\r\n✔️ Họa tiết sọc ngang – trẻ trung, cá tính\r\n✔️ Dáng loose – rộng rãi, thoải mái mọi chuyển động\r\n✔️ Cổ polo lịch sự – dễ mặc, dễ phối\r\n\r\nChiếc áo hoàn hảo cho những ngày muốn', 4, 540000.00, 0, 0, 0, 702000.00, 30.00),
(19, 'Áo khoác denim nam form regular', 'Mạnh mẽ – cá tính – không bao giờ lỗi mốt. Áo khoác denim nam form regular là item kinh điển trong tủ đồ phái mạnh, giúp bạn dễ dàng tạo dấu ấn riêng với phong cách bụi bặm và nam tính. Thiết kế form regular vừa vặn, không quá ôm cũng không quá rộng, phù hợp với nhiều vóc dáng và dễ phối đồ trong mọi hoàn cảnh.\r\n\r\nSản phẩm sử dụng chất liệu denim dày dặn, bền màu, giúp giữ form tốt và mang lại cảm giác chắc chắn khi mặc. Đường may tỉ mỉ, chi tiết túi trước ngực và khuy kim loại tạo nên sự cân đối giữa cổ điển và hiện đại. Dễ dàng kết hợp với áo thun, hoodie hoặc sơ mi bên trong – từ dạo phố, đi làm đến đi chơi đều phù hợp.\r\n\r\n✔️ Denim cao cấp – đứng dáng, chắc tay, lâu phai màu\r\n✔️ Form regular – dễ mặc, dễ phối\r\n✔️ Thiết kế cổ điển – cá tính, nam tính, thời trang\r\n✔️ Tính ứng dụng cao – mặc quanh năm, phối được nhiều phong cách\r\n\r\nChiếc áo khoác denim', 5, 736000.00, 0, 0, 0, 956800.00, 30.00),
(20, 'Quần lót nam organic cotton .Boxer', 'Thoải mái từ bên trong – quần lót nam organic cotton .Boxer mang đến cảm giác dễ chịu tối đa cho những chuyển động cả ngày dài. Được làm từ cotton hữu cơ (organic cotton) thân thiện với làn da và môi trường, chất vải mềm mịn, co giãn vừa phải, giúp ôm nhẹ nhưng không gây bó sát hay khó chịu.\r\n\r\nThiết kế dạng boxer suông nhẹ, thoáng khí, phù hợp cho những ai yêu thích sự thoải mái và tự do trong từng bước di chuyển. Cạp thun mềm, đàn hồi tốt, không để lại vết hằn trên da. Màu sắc tối giản, dễ sử dụng hàng ngày.\r\n\r\n✔️ Organic cotton – an toàn cho da, thân thiện môi trường\r\n✔️ Dáng boxer – thoáng mát, không bó sát\r\n✔️ Cạp thun mềm – co giãn tốt, giữ form ổn định\r\n✔️ Thiết kế đơn giản – tiện dụng, tinh tế\r\n\r\nSự lựa chọn hoàn hảo cho những quý ông đề cao cảm giác thoải mái và chất lượng bền vững.', 6, 117000.00, 0, 0, 0, 152100.00, 30.00),
(21, 'Quần lót nam organic cotton phối lưng.Brief', 'Gọn gàng – thoải mái – chuẩn tinh tế. Quần lót nam organic cotton phối lưng.Brief được thiết kế dành riêng cho những quý ông ưu tiên cảm giác dễ chịu mà vẫn chú trọng đến phom dáng. Với chất liệu organic cotton thân thiện với làn da, sản phẩm mang lại độ mềm mại, thoáng khí và thấm hút mồ hôi hiệu quả – lý tưởng để mặc hằng ngày.\r\n\r\nKiểu dáng brief ôm gọn, hỗ trợ tốt mà không gây cấn hoặc khó chịu. Phần lưng thun phối màu hoặc họa tiết, đàn hồi tốt, tạo điểm nhấn cá tính nhưng không kém phần sang trọng. Sự kết hợp giữa tính năng và thiết kế giúp chiếc brief này vừa tiện dụng, vừa thời trang.\r\n\r\n✔️ Vải cotton hữu cơ – mềm mịn, lành tính, bảo vệ môi trường\r\n✔️ Dáng brief – ôm gọn, hỗ trợ tối đa\r\n✔️ Lưng phối nổi bật – cạp thun chắc chắn, không hằn da\r\n✔️ Phù hợp mặc hằng ngày – từ đi làm, thể thao đến nghỉ ngơi\r\n\r\nMột chiếc quần lót không chỉ để mặc – mà để bạn cảm thấy tự tin & thoải mái suốt cả ngày.', 6, 98000.00, 0, 0, 0, 127400.00, 30.00),
(22, 'Quần Lót Nam Organic Cotton Trơn phối lưng. Boxer', 'Tối giản nhưng chuẩn mực – quần lót nam Organic Cotton trơn phối lưng .Boxer là lựa chọn hoàn hảo cho những chàng trai yêu thích cảm giác thoải mái, nhẹ nhàng và an toàn cho làn da. Sử dụng chất liệu organic cotton cao cấp, quần mang đến sự mềm mại, thoáng khí và thấm hút mồ hôi tốt – thích hợp để mặc suốt cả ngày dài, kể cả khi vận động nhiều.\r\n\r\nKiểu dáng boxer suông nhẹ, giúp ôm vừa vặn cơ thể mà không gây bí bách hay hằn da. Phần lưng thun bản to phối màu/hoạ tiết, tạo điểm nhấn tinh tế và giúp quần giữ form tốt, không bị cuộn hay xô lệch khi mặc.\r\n\r\n✔️ 100% organic cotton – an toàn, không gây kích ứng\r\n✔️ Dáng boxer – thoáng mát, không bó sát\r\n✔️ Thiết kế trơn – đơn giản, tinh tế\r\n✔️ Cạp thun phối – chắc chắn, tôn vẻ hiện đại\r\n\r\nMặc đơn giản nhưng cảm nhận sự chỉn chu từ bên trong – lựa chọn xứng tầm cho phái mạnh đề cao chất lượng và sự thoải mái.', 6, 94000.00, 0, 0, 0, 122200.00, 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `color_id` int(11) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `image`, `stock`, `color_id`, `size_id`) VALUES
(1, 1, '10f24kni004-ivory-ao-len-tay-ngan-nam-1-jpg-uz8v.jpg', 50, 8, 4),
(2, 1, '10f24kni004-ivory-ao-len-tay-ngan-nam-1-jpg-uz8v.jpg', 40, 8, 3),
(3, 1, '10f24kni004-ivory-ao-len-tay-ngan-nam-1-jpg-uz8v.jpg', 35, 8, 5),
(4, 1, '10f24kni004-navy-ao-len-tay-ngan-nam-1-jpg-kah0.jpg', 50, 12, 5),
(5, 1, '10f24kni004-navy-ao-len-tay-ngan-nam-1-jpg-kah0.jpg', 50, 12, 4),
(6, 2, '10f24pol023-d-sapphire-rainy-day-ao-polo-nam-1-jpg-4u8s.jpg', 50, 7, 2),
(7, 2, '10f24pol023-rainy-day-covert-green-ao-polo-nam-1-jpg-qcn0.jpg', 60, 2, 3),
(8, 2, '10f24pol023-d-sapphire-rainy-day-ao-polo-nam-1-jpg-4u8s.jpg', 50, 7, 3),
(9, 3, '10s25shl015-black-ao-so-mi-nam-1-jpg-o1t0.jpg', 50, 1, 4),
(10, 3, '10s25shl015-white-ao-so-mi-nam-1-jpg-4e7k.jpg', 50, 8, 4),
(11, 3, '10s25shl015-black-ao-so-mi-nam-1-jpg-o1t0.jpg', 50, 1, 5),
(12, 3, '10s25shl015-white-ao-so-mi-nam-1-jpg-4e7k.jpg', 50, 8, 5),
(13, 4, '10f24jac005-dried-sage-ao-khoac-nam-1-jpg-7q20.jpg', 50, 6, 5),
(14, 4, '10f24jac005-silver-birch-ao-khoac-nam-1-jpg-3nvj.jpg', 50, 8, 4),
(15, 4, '10f24jac005-black-ao-khoac-nam-1-jpg-x1gs.jpg', 50, 1, 4),
(16, 4, '10f24jac005-silver-birch-ao-khoac-nam-1-jpg-3nvj.jpg', 50, 8, 3),
(17, 4, '10f24jac005-black-ao-khoac-nam-1-jpg-x1gs.jpg', 50, 1, 5),
(18, 5, '10F24PKS001_BITTER-CHOCO_1_quan-short-nam-1-ubui.jpg', 50, 13, 2),
(19, 5, '10F24PKS001_BITTER-CHOCO_1_quan-short-nam-1-ubui.jpg', 50, 13, 1),
(20, 5, '10F24PKS001_BLACK_1_quan-short-nam-1-ocls.jpg', 50, 1, 3),
(21, 5, '10F24PKS001_BLACK_1_quan-short-nam-1-ocls.jpg', 50, 1, 1),
(22, 5, '10F24PKS001_COVERT-GREEN_1_quan-short-nam-1-rgiw.jpg', 50, 2, 2),
(23, 5, '10F24PKS001_COVERT-GREEN_1_quan-short-nam-1-rgiw.jpg', 50, 2, 3),
(24, 5, '10F24PKS001_PUMICE-STONE_quan-short-nam-1-jkaw.jpg', 50, 8, 1),
(25, 5, '10F24PKS001_PUMICE-STONE_quan-short-nam-1-jkaw.jpg', 50, 8, 2),
(26, 6, '10s25tss048-matcha-latte-1-jpg-6tuh.jpg', 50, 6, 3),
(27, 6, '10s25tss048-matcha-latte-1-jpg-6tuh.jpg', 50, 6, 4),
(28, 6, '10s25tss048-strawberry-1-jpg-ayc7.jpg', 50, 9, 3),
(29, 6, '10s25tss048-strawberry-1-jpg-ayc7.jpg', 50, 9, 4),
(30, 6, '10s25tss048-strawberry-1-jpg-ayc7.jpg', 60, 9, 5),
(31, 7, '10s25tss008-cool-brew-1-jpg-xdx8.jpg', 50, 3, 2),
(32, 7, '10s25tss008-cool-brew-1-jpg-xdx8.jpg', 50, 3, 3),
(33, 7, '10s25tss008-cool-brew-1-jpg-xdx8.jpg', 70, 3, 4),
(34, 7, '10s25tss008-brown-rice-1-jpg-vnjs.jpg', 70, 2, 3),
(35, 7, '10s25tss008-brown-rice-1-jpg-vnjs.jpg', 50, 2, 4),
(36, 7, '10s25tss008-brown-rice-1-jpg-vnjs.jpg', 70, 2, 5),
(37, 8, '10S24SHL002C_WHITE_ao-so-mi-nam-dai-tay-1-szlg.jpg', 50, 8, 6),
(38, 8, '10S24SHL002C_WHITE_ao-so-mi-nam-dai-tay-1-szlg.jpg', 50, 8, 5),
(39, 8, '10S24SHL002C_WHITE_ao-so-mi-nam-dai-tay-1-szlg.jpg', 50, 8, 2),
(40, 8, '10s24shl002c-blue-ao-so-mi-nam-1-jpg-6bdu.jpg', 80, 11, 5),
(41, 8, '10s24shl002c-blue-ao-so-mi-nam-1-jpg-6bdu.jpg', 50, 7, 3),
(42, 9, '10f24dps002-l-indigo-quan-short-nam-1-jpg-88kg.jpg', 50, 7, 3),
(43, 9, '10f24dps002-l-indigo-quan-short-nam-1-jpg-88kg.jpg', 50, 7, 2),
(44, 10, '10s25pol015-black-1-jpg-soav.jpg', 50, 1, 1),
(45, 10, '10s25pol015-black-1-jpg-soav.jpg', 50, 1, 2),
(46, 10, '10s25pol015-black-1-jpg-soav.jpg', 50, 1, 3),
(47, 10, '10s25pol015-white-alyssum-1-jpg-3yjd.jpg', 50, 8, 3),
(48, 10, '10s25pol015-white-alyssum-1-jpg-3yjd.jpg', 50, 8, 4),
(49, 10, '10s25pol015-white-alyssum-1-jpg-3yjd.jpg', 50, 8, 6),
(50, 11, '10s25shs016-l-blue-ao-so-mi-nam-1-jpg-9zxg.jpg', 50, 11, 5),
(51, 11, '10s25shs016-m-blue-ao-so-mi-nam-1-jpg-8og0.jpg', 50, 11, 6),
(52, 11, '10s25shs016-l-blue-ao-so-mi-nam-1-jpg-9zxg.jpg', 50, 7, 1),
(53, 11, '10s25shs016-m-blue-ao-so-mi-nam-1-jpg-8og0.jpg', 50, 11, 1),
(54, 12, '10s24shl006c-covert-green-1-ao-so-mi-nam-1-jpg.jpg', 50, 10, 2),
(55, 12, '10s24shl006c-covert-green-1-ao-so-mi-nam-1-jpg.jpg', 50, 10, 3),
(56, 12, '10s24shl006c-covert-green-1-ao-so-mi-nam-1-jpg.jpg', 50, 10, 4),
(57, 12, '10s24shl006-blue-1-jpg-3n6n.jpg', 50, 11, 5),
(58, 12, '10s24shl006-blue-1-jpg-3n6n.jpg', 50, 11, 6),
(59, 12, '10s24shl006-blue-1-jpg-3n6n.jpg', 50, 11, 3),
(60, 13, '10f24jac004-black-ao-khoac-nam-5-jpg-g0vc.jpg', 50, 1, 3),
(61, 13, '10f24jac004-black-ao-khoac-nam-5-jpg-g0vc.jpg', 50, 1, 4),
(62, 13, '10f24jac004-black-ao-khoac-nam-5-jpg-g0vc.jpg', 50, 1, 5),
(63, 13, '10f24jac004-silver-birch-ao-khoac-nam-5-jpg-3uy2.jpg', 40, 8, 4),
(64, 13, '10f24jac004-silver-birch-ao-khoac-nam-5-jpg-3uy2.jpg', 40, 8, 5),
(65, 13, '10f24jac004-silver-birch-ao-khoac-nam-5-jpg-3uy2.jpg', 50, 8, 6),
(66, 14, '10F23SWE001_BLUE-QUARTZ_ao-sweater-nam-3-hfyp.jpg', 50, 7, 2),
(67, 14, '10F23SWE001_BLUE-QUARTZ_ao-sweater-nam-3-hfyp.jpg', 50, 7, 3),
(68, 14, '10F23SWE001_BLUE-QUARTZ_ao-sweater-nam-3-hfyp.jpg', 43, 7, 4),
(69, 14, '10F23SWE001_BLACK_ao-sweater-nam-3-nmkb.jpg', 50, 1, 1),
(70, 14, '10F23SWE001_BLACK_ao-sweater-nam-3-nmkb.jpg', 50, 1, 6),
(71, 15, '10s25tss003-bark-1-jpg-kg4r.jpg', 50, 5, 5),
(72, 15, '10s25tss003-bark-1-jpg-kg4r.jpg', 54, 5, 6),
(73, 15, '10s25tss003-white-1-jpg-hp1y.jpg', 50, 8, 2),
(74, 15, '10s25tss003-white-1-jpg-hp1y.jpg', 65, 8, 3),
(78, 16, '10s25tss006-cool-brew-1-jpg-7zno.jpg', 50, 3, 4),
(79, 16, '10s25tss006-cool-brew-1-jpg-7zno.jpg', 50, 3, 5),
(80, 16, '10s25tss006-cool-brew-1-jpg-7zno.jpg', 50, 3, 6),
(81, 17, '10s24pol004p-bright-white-ao-polo-nam-6-jpg-2233.jpg', 50, 8, 1),
(82, 17, '10s24pol004p-bright-white-ao-polo-nam-6-jpg-2233.jpg', 50, 8, 2),
(83, 17, '10s24pol004p-bright-white-ao-polo-nam-6-jpg-2233.jpg', 50, 8, 3),
(84, 17, '10s24pol004p-dark-sapphire-ao-polo-nam-6-jpg-y684.jpg', 50, 1, 3),
(85, 17, '10s24pol004p-dark-sapphire-ao-polo-nam-6-jpg-y684.jpg', 50, 1, 4),
(86, 17, '10s24pol004p-dark-sapphire-ao-polo-nam-6-jpg-y684.jpg', 50, 1, 5),
(87, 18, '10f24pol012-navy-blazer-ao-polo-nam-1-jpg-o7bd.jpg', 50, 7, 3),
(88, 18, '10f24pol012-navy-blazer-ao-polo-nam-1-jpg-o7bd.jpg', 50, 7, 4),
(89, 18, '10f24pol012-white-ao-polo-nam-1-jpg-7idj.jpg', 50, 8, 4),
(90, 18, '10f24pol012-white-ao-polo-nam-1-jpg-7idj.jpg', 50, 8, 5),
(91, 19, '10f24dja002-d-grey-ao-khoac-jean-nam-5-jpg-kyn2.jpg', 50, 1, 4),
(92, 19, '10f24dja002-d-grey-ao-khoac-jean-nam-5-jpg-kyn2.jpg', 50, 1, 5),
(93, 19, '10f24dja002-d-grey-ao-khoac-jean-nam-5-jpg-kyn2.jpg', 50, 1, 6),
(94, 20, '10s25und001-white-jpg-um7a.jpg', 50, 8, 1),
(95, 20, '10s25und001-white-jpg-um7a.jpg', 50, 8, 2),
(96, 20, '10s25und001-white-jpg-um7a.jpg', 50, 8, 4),
(97, 20, '10s25und001-navy-jpg-jf6p.jpg', 50, 12, 3),
(98, 20, '10s25und001-navy-jpg-jf6p.jpg', 50, 12, 4),
(99, 20, '10s25und001-navy-jpg-jf6p.jpg', 50, 12, 5),
(100, 21, '10s25und002-grey-jpg-imo1.jpg', 50, 4, 1),
(101, 21, '10s25und002-grey-jpg-imo1.jpg', 50, 4, 2),
(102, 21, '10s25und002-grey-jpg-imo1.jpg', 50, 4, 3),
(103, 21, '10s25und002-navy-jpg-8v44.jpg', 50, 12, 2),
(104, 21, '10s25und002-navy-jpg-8v44.jpg', 50, 12, 3),
(105, 21, '10s25und002-navy-jpg-8v44.jpg', 50, 12, 4),
(106, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 1),
(107, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 2),
(108, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 3),
(109, 22, '10f24und005-grey-boxer-nam-1-jpg-9h5j.jpg', 50, 4, 2),
(110, 22, '10f24und005-grey-boxer-nam-1-jpg-9h5j.jpg', 50, 4, 3),
(111, 22, '10f24und005-grey-boxer-nam-1-jpg-9h5j.jpg', 50, 4, 4),
(112, 22, '10f24und005-navy-boxer-nam-1-jpg-7eq3.jpg', 50, 12, 2),
(113, 22, '10f24und005-navy-boxer-nam-1-jpg-7eq3.jpg', 50, 12, 3),
(114, 22, '10f24und005-navy-boxer-nam-1-jpg-7eq3.jpg', 50, 12, 4),
(115, 22, '10f24und005-black-boxer-nam-1-jpg-2ibv.jpg', 50, 1, 2),
(116, 22, '10f24und005-black-boxer-nam-1-jpg-2ibv.jpg', 50, 1, 3),
(117, 22, '10f24und005-black-boxer-nam-1-jpg-2ibv.jpg', 60, 1, 4),
(118, 5, '10F24PKS001_BLACK_1_quan-short-nam-1-ocls.jpg', 50, 1, 2),
(119, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `rate_id` int(11) NOT NULL,
  `price_min` decimal(12,2) DEFAULT NULL,
  `price_max` decimal(12,2) DEFAULT NULL,
  `rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`) VALUES
(1, 'user'),
(2, 'admin'),
(3, 'manager'),
(4, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission_details`
--

CREATE TABLE `role_permission_details` (
  `role_permission_detail_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission_details`
--

INSERT INTO `role_permission_details` (`role_permission_detail_id`, `role_id`, `permission_id`, `action`) VALUES
(1, 2, 1, 'read'),
(2, 2, 1, 'write'),
(3, 2, 1, 'delete'),
(4, 2, 2, 'read'),
(5, 2, 2, 'write'),
(6, 2, 2, 'delete'),
(7, 2, 3, 'read'),
(8, 2, 3, 'write'),
(9, 2, 3, 'delete'),
(10, 2, 4, 'read'),
(11, 2, 4, 'write'),
(12, 2, 4, 'delete'),
(13, 2, 5, 'read'),
(14, 2, 5, 'write'),
(15, 2, 5, 'delete'),
(16, 2, 6, 'read'),
(17, 2, 6, 'write'),
(18, 2, 6, 'delete'),
(19, 3, 1, 'read'),
(20, 3, 1, 'write'),
(21, 3, 2, 'read'),
(22, 3, 2, 'write'),
(23, 3, 3, 'read'),
(24, 3, 3, 'write'),
(25, 3, 4, 'read'),
(26, 3, 4, 'write'),
(27, 3, 5, 'read'),
(28, 4, 1, 'read'),
(29, 4, 2, 'read'),
(30, 4, 2, 'write'),
(31, 4, 4, 'read');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `name`) VALUES
(6, '2XL'),
(3, 'L'),
(2, 'M'),
(1, 'S'),
(4, 'X'),
(5, 'XL');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `name`, `email`, `address`) VALUES
(1, 'Công ty TNHH Minh Tâm', 'minhtam@supplier.vn', '123 Nguyễn Trãi, Q5, TP.HCM'),
(2, 'Công ty CP StyleMax', 'stylemax@supplier.vn', '45 Lê Lai, Q1, TP.HCM'),
(3, 'Công ty TNHH Gia Khang', 'giakhang@supplier.vn', '78 Cộng Hòa, Q.Tân Bình, TP.HCM'),
(4, 'Công ty TNHH Song Hành', 'songhanh@supplier.vn', '56 Hai Bà Trưng, Q1, TP.HCM'),
(5, 'Công ty TNHH UrbanLook', 'urbanlook@supplier.vn', '88 Trần Hưng Đạo, Q5, TP.HCM'),
(6, 'Công ty TNHH Nhật Quang', 'nhatquang@supplier.vn', '101 Trường Chinh, Q.Tân Phú, TP.HCM'),
(7, 'Công ty TNHH ElegantCo', 'elegantco@supplier.vn', '12 Phạm Văn Đồng, Q.Thủ Đức, TP.HCM'),
(8, 'Công ty CP Hoàng Gia', 'hoanggia@supplier.vn', '99 Nguyễn Thái Học, Q1, TP.HCM'),
(9, 'Công ty TNHH Tâm Đức', 'tamduc@supplier.vn', '67 Phan Văn Trị, Q.Bình Thạnh, TP.HCM'),
(10, 'Công ty TNHH SmartWear', 'smartwear@supplier.vn', '88 Nguyễn Văn Linh, Q.7, TP.HCM'),
(11, 'Công ty TNHH Nam Phong', 'namphong@supplier.vn', '145 Trần Quang Khải, Q1, TP.HCM'),
(12, 'Công ty TNHH Fashina', 'fashina@supplier.vn', '22 Nguyễn Văn Cừ, Q10, TP.HCM'),
(13, 'Công ty CP Đại Hưng', 'daihung@supplier.vn', '200 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM'),
(14, 'Công ty TNHH GoldStyle', 'goldstyle@supplier.vn', '33 Cách Mạng Tháng 8, Q3, TP.HCM'),
(15, 'Công ty TNHH Tín Nghĩa', 'tinnghia@supplier.vn', '77 Nguyễn Văn Đậu, Q.Bình Thạnh, TP.HCM'),
(16, 'Công ty TNHH EverVibe', 'evervibe@supplier.vn', '144 Lý Chính Thắng, Q3, TP.HCM'),
(17, 'Công ty TNHH Bình Minh', 'binhminh@supplier.vn', '10 Nguyễn Kiệm, Q.Phú Nhuận, TP.HCM'),
(18, 'Công ty TNHH M&T Distributors', 'mt@supplier.vn', '55 Võ Thị Sáu, Q1, TP.HCM'),
(19, 'Công ty TNHH Alpha Zone', 'alphazone@supplier.vn', '17 Nguyễn Hữu Cảnh, Q.Bình Thạnh, TP.HCM'),
(20, 'Công ty CP Phúc Hưng', 'phuchung@supplier.vn', '29 Phạm Ngũ Lão, Q1, TP.HCM');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `phone`, `address`, `role_id`, `status`) VALUES
(1, 'admin', 'adminpass', 'admin@example.com', '0900000000', 'TP.HCM', 2, 1),
(2, 'khach1', 'khachpass', 'khach1@example.com', '0900000001', 'Q1, TP.HCM', 1, 1),
(3, 'staff1', 'staffpass', 'staff1@example.com', '0900000002', 'Q3, TP.HCM', 4, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_details`
--
ALTER TABLE `cart_details`
  ADD PRIMARY KEY (`cart_detail_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `importreceipt`
--
ALTER TABLE `importreceipt`
  ADD PRIMARY KEY (`ImportReceipt_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  ADD PRIMARY KEY (`ImportReceipt_details_id`),
  ADD KEY `ImportReceipt_id` (`ImportReceipt_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `rate_id` (`rate_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `fk_size_id` (`size_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`rate_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permission_details`
--
ALTER TABLE `role_permission_details`
  ADD PRIMARY KEY (`role_permission_detail_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_details`
--
ALTER TABLE `cart_details`
  MODIFY `cart_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `importreceipt`
--
ALTER TABLE `importreceipt`
  MODIFY `ImportReceipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  MODIFY `ImportReceipt_details_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_permission_details`
--
ALTER TABLE `role_permission_details`
  MODIFY `role_permission_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cart_details`
--
ALTER TABLE `cart_details`
  ADD CONSTRAINT `cart_details_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `cart_details_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `importreceipt`
--
ALTER TABLE `importreceipt`
  ADD CONSTRAINT `importreceipt_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `importreceipt_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  ADD CONSTRAINT `importreceipt_details_ibfk_1` FOREIGN KEY (`ImportReceipt_id`) REFERENCES `importreceipt` (`ImportReceipt_id`),
  ADD CONSTRAINT `importreceipt_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `importreceipt_details_ibfk_3` FOREIGN KEY (`rate_id`) REFERENCES `rates` (`rate_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `order_details_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_size_id` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`),
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_variants_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`color_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `role_permission_details`
--
ALTER TABLE `role_permission_details`
  ADD CONSTRAINT `role_permission_details_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `role_permission_details_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
