# my_crawler

use SimplePageCrawler\OnPageSeo as OnPageSeo;

$onpage = new OnPageSeo();

echo '<pre>';
print_r($onpage->get($uri)->toArray());
echo '</pre>';
