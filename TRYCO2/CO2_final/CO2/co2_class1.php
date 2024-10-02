<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/CO2_class.css" type="text/css">
    <link rel="shortcut icon" href="img\logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <!-- 營業車和自用車的差別 -->
        <div id="content1" class="mb-5">
            <h2>營業車和自用車的差別</h2>
            <div class="accordion" id="businessPrivateAccordion">

                <!-- 營業車 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingBusiness">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBusiness" aria-expanded="true" aria-controls="collapseBusiness">
                            營業車
                        </button>
                    </h2>
                    <div id="collapseBusiness" class="accordion-collapse collapse show" aria-labelledby="headingBusiness" data-bs-parent="#businessPrivateAccordion">
                        <div class="accordion-body">
                            <p>
                                直接以公司名義購買自用乘人小客車或向租賃公司承租，<br>
                                例如: 巴士、客運、公車、計程車...，租賃業、客運業、貨運業...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 自用車 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPrivate">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrivate" aria-expanded="false" aria-controls="collapsePrivate">
                            自用車
                        </button>
                    </h2>
                    <div id="collapsePrivate" class="accordion-collapse collapse" aria-labelledby="headingPrivate" data-bs-parent="#businessPrivateAccordion">
                        <div class="accordion-body">
                            <p>
                                非供銷售或提供勞務使用之9人座以下且行車執照登載為「自用小客車」之乘人小汽車。<br>
                                例如: 公司車...
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 車種差別 -->
        <div id="content2" class="mb-5">
            <h2>車種差別</h2>
            <div class="accordion" id="vehicleAccordion">
                <!-- 各種車輛類型 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            小客車
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                            座位在九以下之客車或座位在二十四座以下之幼童專用車其座位之計算包括駕駛人及幼童管理人在內。
                        </div>
                    </div>
                </div>

                <!-- 大客車 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            大客車
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                        座位在十座以上或總重量愈三千五百公斤之客車，座位在二十五座以上或總重愈三千五百公斤之幼童專用車其座位之計算包括駕駛人，幼童管理人及營業車之服務員在內。
                        </div>
                    </div>
                </div>

                <!-- 大貨車 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            大貨車
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                            總重量愈三千五百公斤之貨車。
                        </div>
                    </div>
                </div>

                <!-- 連結車 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            連結車
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                            指汽車與重型拖車所組成之車輛。
                        </div>
                    </div>
                </div>

                <!-- 垃圾車 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            垃圾車
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                            算特種車輛。
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 柴聯車電聯車差別 -->
        <div id="content3" class="mb-5">
            <h2>柴聯車電聯車差別</h2>
            <div class="accordion" id="vehicleAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDiesel">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiesel" aria-expanded="true" aria-controls="collapseDiesel">
                            柴聯車（DMU）案例
                        </button>
                    </h2>
                    <div id="collapseDiesel" class="accordion-collapse collapse show" aria-labelledby="headingDiesel" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li><strong>台鐵「普快車」與「區間車」的部分路段：</strong>柴聯車常見於非電氣化的鐵路路段，例如台鐵東部幹線部分路段或南迴線的列車。這些路段電氣化程度較低，柴聯車不需要架設電車線，因此在這些區段，柴聯車成為主要的交通工具。</li>
                                <li><strong>普悠瑪號（部分柴聯車版本）：</strong>早期普悠瑪號的車型有部分使用柴油發動機，適合在東部非完全電氣化的鐵路路段運行。</li>
                                <li><strong>莒光號（台鐵）：</strong>莒光號的部分列車使用柴聯車車型，這些列車運行於較偏遠或未完全電氣化的路段。</li>
                                <li><strong>南迴線：</strong>在台灣南部的南迴線，過去曾使用柴聯車運行，因該路線長期未完全電氣化。隨著2020年南迴鐵路完成電氣化，這些柴聯車已經逐步減少。</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingElectric">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseElectric" aria-expanded="false" aria-controls="collapseElectric">
                            電聯車（EMU）案例
                        </button>
                    </h2>
                    <div id="collapseElectric" class="accordion-collapse collapse" aria-labelledby="headingElectric" data-bs-parent="#vehicleAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li><strong>台鐵「區間車」與「自強號」的電聯車：</strong>電聯車是運行於電氣化鐵路上的列車，依賴於電力供應系統，常見於台鐵的主要幹線如西部幹線。電聯車具有較高的能效且環保性佳。</li>
                                <li><strong>高鐵：</strong>台灣高鐵使用的是電聯車系統，這種高速鐵路列車全程電氣化，能夠提供高速且穩定的運輸服務。</li>
                                <li><strong>台北捷運與高雄捷運：</strong>兩座城市的捷運系統皆使用電聯車作為主要運輸工具，這些列車依賴架設於路線上的電力系統運行，提供高密度、短間隔的公共交通服務。</li>
                                <li><strong>通勤電聯車：</strong>台鐵通勤區間車（EMU）常見於台灣西部各大都市間，其高效、低碳的特性使其成為民眾每日通勤的重要交通工具。</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div>
            資料來源 : <br>
            <a href="https://reurl.cc/7dLKyb">營業車和自用車的差別</a><br>
            <a href="https://reurl.cc/1b7K4V">車種差別</a><br>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>