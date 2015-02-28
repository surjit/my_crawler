# my_crawler

use SimplePageCrawler\OnPageSeo as OnPageSeo;

$onpage = new OnPageSeo();


print_r($onpage->get($uri)->toArray());
