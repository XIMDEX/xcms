<?php
namespace Ximdex\Nodeviews;

class ViewPrepareHTML extends AbstractView implements IView
{
    /**
     * {@inheritdoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {
        // Get the content
        $content = $this->retrieveContent($pointer);
        
        // Return the pointer to the transformed content
        return $this->storeTmpContent($content);
    }
}